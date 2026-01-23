# Fix TOCTOU Race Condition in Product Slug Creation

## Problem

In `backend/src/routes/products/admin.ts` (lines ~421-454), product creation has a Time-of-Check-Time-of-Use (TOCTOU) race condition:

```typescript
// Step 1: CHECK if slug exists
const { result: existing } = await measureQueryTime('products.checkSlugExists', async () =>
  db.select().from(products).where(eq(products.slug, data.slug)).limit(1)
);
if (existing.length > 0) {
  return c.json({ error: 'Product with this slug already exists' }, 400);
}

// GAP - Another request could insert the same slug here!

// Step 2: INSERT product
const { result: productResult } = await measureQueryTime('products.create', async () =>
  db.insert(products).values({ ...data }).returning()
);
```

The same issue exists in the update route (lines ~488-497).

The database has a UNIQUE constraint on `slug` which prevents duplicates, but the application doesn't handle the constraint violation error gracefully - it would return a generic 500 error instead of a user-friendly message.

## Required Fix

### Option A: Remove the pre-check, rely on DB constraint (Recommended)

Remove the manual uniqueness check and handle the database constraint violation:

```typescript
try {
  const { result: productResult } = await measureQueryTime('products.create', async () =>
    db.insert(products).values({ ...data }).returning()
  );
  // ... success handling
} catch (error: any) {
  // PostgreSQL unique violation error code
  if (error.code === '23505' || error.message?.includes('unique')) {
    return c.json({ error: 'Product with this slug already exists' }, 400);
  }
  throw error; // Re-throw other errors
}
```

### Option B: Use database transaction with serializable isolation

```typescript
const result = await db.transaction(async (tx) => {
  const existing = await tx
    .select()
    .from(products)
    .where(eq(products.slug, data.slug))
    .for('update') // Row-level lock
    .limit(1);

  if (existing.length > 0) {
    throw new Error('SLUG_EXISTS');
  }

  return await tx.insert(products).values({ ...data }).returning();
});
```

### Apply same fix to update route:

The product update endpoint has the same TOCTOU pattern when changing slugs.

## Files to Modify

- `backend/src/routes/products/admin.ts` (create and update handlers)

## Verification

- Concurrent requests with the same slug don't create duplicates
- User gets a clear error message when slug already exists
- No 500 errors from unhandled constraint violations
- Product update with slug change also handles conflicts
- Existing tests still pass
