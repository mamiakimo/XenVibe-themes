# Fix Optional Auth Middleware - Add Banned User Check

## Problem

In `backend/src/middleware/auth.ts`, the `optionalAuthMiddleware` (lines 206-231) does NOT check if a user is blocked/banned, while `authMiddleware` (lines 127-130) does:

**authMiddleware checks banned users:**
```typescript
const dbUser = await db.select().from(users).where(eq(users.id, user.uid)).limit(1);
if (dbUser.length > 0 && dbUser[0].isBlocked) {
  return c.json({ error: 'Forbidden', message: 'Account has been suspended' }, 403);
}
```

**optionalAuthMiddleware does NOT:**
```typescript
export async function optionalAuthMiddleware(c: Context, next: Next) {
  const authHeader = c.req.header('Authorization');
  if (authHeader && authHeader.startsWith('Bearer ')) {
    const token = authHeader.substring(7);
    try {
      const decodedToken = await verifyIdToken(token);
      const user: AuthUser = {
        uid: decodedToken.uid,
        email: decodedToken.email || '',
        // ... sets user without ban check
      };
      c.set('user', user);
    } catch (error) {
      // Token invalid, continue without user
    }
  }
  await next();
}
```

## Risk

A banned user can still authenticate on endpoints using `optionalAuthMiddleware`, receiving personalized content, viewing their reviews, getting recommended products, etc.

## Required Fix

Add a banned user check in `optionalAuthMiddleware`. If the user is blocked, clear the user context and continue as unauthenticated:

```typescript
export async function optionalAuthMiddleware(c: Context, next: Next) {
  const authHeader = c.req.header('Authorization');
  if (authHeader && authHeader.startsWith('Bearer ')) {
    const token = authHeader.substring(7);
    try {
      const decodedToken = await verifyIdToken(token);

      // Check if user is blocked
      const dbUser = await db.select().from(users).where(eq(users.id, decodedToken.uid)).limit(1);
      if (dbUser.length > 0 && dbUser[0].isBlocked) {
        // Banned user - continue without authentication
        await next();
        return;
      }

      const user: AuthUser = {
        uid: decodedToken.uid,
        email: decodedToken.email || '',
        emailVerified: decodedToken.email_verified || false,
        displayName: decodedToken.name,
        photoURL: decodedToken.picture,
        isAdmin: isAdminEmail(decodedToken.email),
      };
      c.set('user', user);
    } catch (error) {
      // Token invalid, continue without user
    }
  }
  await next();
}
```

## Files to Modify

- `backend/src/middleware/auth.ts`

## Verification

- Banned users on optional-auth endpoints get treated as unauthenticated
- Non-banned users continue to work normally
- Performance: Consider caching the ban check result briefly if this middleware is called frequently
