# Fix Missing Foreign Key Constraints

## Problem

In `backend/src/db/schema/licenses.ts` (line ~38), the `orderId` field is defined WITHOUT a foreign key reference:

```typescript
orderId: integer('order_id'),  // NO foreign key constraint!
```

This allows:
- Orphaned license records with invalid order IDs
- Data integrity violations
- No referential integrity enforcement

Meanwhile, `orderItems` in `orders.ts` correctly defines foreign keys:
```typescript
orderId: integer('order_id')
  .notNull()
  .references(() => orders.id),  // Correct
```

## Required Fix

### 1. Add foreign key to licenses.orderId:

```typescript
orderId: integer('order_id')
  .references(() => orders.id),  // Add FK reference
```

### 2. Generate a migration for this change:

After modifying the schema, generate a new Drizzle migration:

```bash
cd backend
npx drizzle-kit generate
```

This will create a migration that adds the foreign key constraint to the existing table.

### 3. Review the generated migration

Ensure the migration:
- Adds the FK constraint
- Doesn't drop existing data
- Handles NULL values correctly (orderId is nullable)

## Files to Modify

- `backend/src/db/schema/licenses.ts`
- New migration file (auto-generated)

## Verification

- The FK constraint is added to the licenses table
- Existing data is not affected (NULL orderId values remain valid)
- New licenses with invalid orderId values are rejected by the database
- The migration runs successfully on the dev database
- Run: `docker exec zyzoom_dev_php` equivalent for this project's DB container
