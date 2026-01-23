# Fix License Key Enumeration Vulnerability

## Problem

In `backend/src/routes/licenses/verification.ts`, the GET `/verify/:licenseKey` endpoint reveals whether a license key exists or not through different HTTP status codes and response bodies:

**Current behavior:**
- Valid key: HTTP 200 with license data
- Invalid/non-existent key: HTTP **404** with `status: 'not_found'`

```typescript
if (!license) {
  return c.json(
    {
      valid: false,
      status: 'not_found',  // Reveals key doesn't exist
      error: 'Invalid license key',
    },
    404  // Different status code reveals non-existence
  );
}
```

This allows attackers to brute-force license keys and determine which ones are valid in the system.

## Required Fix

Return a consistent, generic response for all invalid states (not found, expired, revoked, etc.):

```typescript
if (!license) {
  return c.json(
    {
      valid: false,
      status: 'invalid',
      error: 'License verification failed',
    },
    400  // Use 400 for all invalid cases
  );
}
```

Also ensure that timing is consistent - use a constant-time comparison or add artificial delay to prevent timing-based enumeration:

```typescript
// Add near the top of the handler
const startTime = Date.now();

// Before returning any response
const elapsed = Date.now() - startTime;
const minResponseTime = 100; // ms
if (elapsed < minResponseTime) {
  await new Promise(resolve => setTimeout(resolve, minResponseTime - elapsed));
}
```

## Files to Modify

- `backend/src/routes/licenses/verification.ts`

## Verification

- All invalid license states return the same HTTP status code (400)
- All invalid license states return the same response structure
- Response timing is consistent regardless of whether key exists
- Valid licenses still return correct data with HTTP 200
- Rate limiting is still applied on this endpoint
