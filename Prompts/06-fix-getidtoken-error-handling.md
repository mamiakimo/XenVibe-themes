# Fix getIdToken Calls Without Error Handling

## Problem

Multiple places in the frontend call `getIdToken()` without try-catch. If Firebase auth fails (network issues, expired session, etc.), these throw uncaught errors that crash the application.

### Affected Locations:

**1. `frontend/src/lib/api.ts` (line ~189-194):**
```typescript
if (authenticated) {
  const token = await getIdToken();  // No try-catch
  if (token) {
    (headers as Record<string, string>)['Authorization'] = `Bearer ${token}`;
  }
}
```

**2. `frontend/src/lib/auth-context.tsx` (line ~173-174):**
```typescript
const token = await getIdToken();  // No try-catch
if (!token || !user) return;
```

**3. `frontend/src/lib/auth-context.tsx` (line ~204-205):**
```typescript
const token = await getIdToken();  // No try-catch
if (!token) return;
```

## Required Fix

### Fix 1: api.ts - Graceful degradation on auth failure

```typescript
if (authenticated) {
  try {
    const token = await getIdToken();
    if (token) {
      (headers as Record<string, string>)['Authorization'] = `Bearer ${token}`;
    }
  } catch (error) {
    // Auth failed - proceed without token for optional auth,
    // or throw for required auth
    console.warn('Failed to get auth token:', error);
    // Optionally: throw new ApiError('Authentication failed', 401);
  }
}
```

### Fix 2: auth-context.tsx - Handle token retrieval failures

```typescript
let token: string | null = null;
try {
  token = await getIdToken();
} catch (error) {
  console.warn('Failed to get ID token:', error);
  return;
}
if (!token || !user) return;
```

## Files to Modify

- `frontend/src/lib/api.ts`
- `frontend/src/lib/auth-context.tsx`

## Verification

- App doesn't crash when Firebase auth is unavailable
- App doesn't crash on network failures during token retrieval
- Authenticated API calls gracefully fail when token can't be obtained
- User is redirected to login if token retrieval consistently fails
- No uncaught promise rejections in console
