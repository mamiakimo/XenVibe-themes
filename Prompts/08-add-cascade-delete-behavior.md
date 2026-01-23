# Add Cascade Delete Behavior to Foreign Keys

## Problem

All foreign key definitions across the schema files lack `onDelete` specifications. Without explicit behavior, PostgreSQL defaults to `RESTRICT` which prevents deletion of parent records if children exist.

### Affected Tables and Fields:

**licenses.ts:**
```typescript
userId: varchar('user_id', { length: 128 }).notNull().references(() => users.id),
// orderId - already missing FK (separate issue)
productId: integer('product_id').notNull().references(() => products.id),
```

**orders.ts (orderItems):**
```typescript
orderId: integer('order_id').notNull().references(() => orders.id),
productId: integer('product_id').notNull().references(() => products.id),
```

**reviews.ts:**
```typescript
userId: varchar('user_id').references(() => users.id),
productId: integer('product_id').references(() => products.id),
// review_helpful_votes
reviewId: integer('review_id').references(() => productReviews.id),
userId: varchar('user_id').references(() => users.id),
// review_responses
reviewId: integer('review_id').references(() => productReviews.id),
userId: varchar('user_id').references(() => users.id),
```

**tickets.ts:**
```typescript
userId: varchar('user_id').references(() => users.id),
```

## Required Fix

Add appropriate `onDelete` behavior based on the relationship type:

### Cascade (delete children when parent is deleted):
- `orderItems.orderId` -> CASCADE (delete items when order deleted)
- `review_helpful_votes.reviewId` -> CASCADE (delete votes when review deleted)
- `review_responses.reviewId` -> CASCADE (delete responses when review deleted)

### Set Null (preserve record but remove reference):
- `licenses.userId` -> SET NULL (keep license record for audit)
- `licenses.productId` -> SET NULL (keep license for history)
- `orders.userId` -> SET NULL (keep order for accounting)
- `reviews.userId` -> SET NULL (keep review, show as "deleted user")
- `tickets.userId` -> SET NULL (keep ticket for records)

### Restrict (prevent deletion - current default, keep as-is):
- `orderItems.productId` -> RESTRICT (don't delete product with orders)

### Example syntax:

```typescript
orderId: integer('order_id')
  .notNull()
  .references(() => orders.id, { onDelete: 'cascade' }),

userId: varchar('user_id', { length: 128 })
  .notNull()
  .references(() => users.id, { onDelete: 'set null' }),
```

## Files to Modify

- `backend/src/db/schema/licenses.ts`
- `backend/src/db/schema/orders.ts`
- `backend/src/db/schema/reviews.ts`
- `backend/src/db/schema/tickets.ts`
- Generate new migration after changes

## Important Considerations

- For `SET NULL`, the column must be nullable (remove `.notNull()` if present)
- Test that existing data constraints are not violated
- Consider using soft deletes instead of hard deletes for critical data (users, orders)
- The migration should be reviewed carefully before applying to production

## Verification

- All FK definitions have explicit `onDelete` behavior
- Deleting a user sets their references to NULL (not cascading order/license deletion)
- Deleting an order cascades to order items
- Deleting a review cascades to votes and responses
- Products with orders cannot be deleted (RESTRICT)
- Migration runs without errors
