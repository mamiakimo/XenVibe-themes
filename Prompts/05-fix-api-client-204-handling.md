# Fix API Client Crash on 204 No Content Responses

## Problem

In `frontend/src/lib/api.ts` (line ~225), the API client calls `response.json()` unconditionally after a successful response, without checking if the response has a body:

```typescript
const response = await fetch(`${API_URL}${endpoint}`, {
  ...rest,
  headers,
  signal: controller.signal,
});

clearTimeout(timeoutId);

if (!response.ok) {
  const error = await response.json().catch(() => ({ message: 'Request failed' }));
  throw new ApiError(/* ... */);
}

return response.json();  // CRASHES on 204 No Content
```

HTTP 204 responses pass the `.ok` check (status 200-299) but have no body. Calling `.json()` on them throws a SyntaxError.

## Required Fix

Check the response status and content-type before parsing:

```typescript
if (!response.ok) {
  const error = await response.json().catch(() => ({ message: 'Request failed' }));
  throw new ApiError(/* ... */);
}

// Handle 204 No Content and empty responses
if (response.status === 204 || response.headers.get('content-length') === '0') {
  return null as T;
}

// Try to parse JSON, return null if empty
const text = await response.text();
if (!text) {
  return null as T;
}

return JSON.parse(text) as T;
```

## Files to Modify

- `frontend/src/lib/api.ts`

## Verification

- API calls that return 204 no longer crash
- API calls with JSON responses still work correctly
- API calls with empty bodies return null gracefully
- TypeScript types remain correct (T | null if needed, or adjust return type)
- Test DELETE endpoints and other operations that commonly return 204
