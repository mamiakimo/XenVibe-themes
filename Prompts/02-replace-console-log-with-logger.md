# Replace console.log with Structured Logger in Webhooks

## Problem

In `backend/src/routes/webhooks.ts`, there are 24 `console.log` statements mixed with 11 proper `logger.*` calls. The file already imports and initializes the structured logger:

```typescript
import { createLogger } from '../lib/logger';
const logger = createLogger('webhooks');
```

But 68.6% of logging still uses `console.log`, which:
- Doesn't redact sensitive data (emails, amounts, session IDs)
- Produces unstructured output that can't be parsed by log aggregators
- Has no log levels for filtering
- Leaks potentially sensitive information in production

## The Structured Logger

The project has a full-featured logger at `backend/src/lib/logger.ts` that provides:
- JSON formatted logs
- Sensitive data redaction (license keys, API keys, passwords, tokens)
- Log levels (trace, debug, info, warn, error, fatal)
- Child logger support for request context
- Error serialization with stack traces

## Required Fix

Replace ALL `console.log`, `console.error`, and `console.warn` statements in `webhooks.ts` with appropriate logger methods:

### Mapping:
- `console.log` with informational messages -> `logger.info(message, { context })`
- `console.log` with debug/trace info -> `logger.debug(message, { context })`
- `console.error` -> `logger.error(message, { error, context })`
- `console.warn` -> `logger.warn(message, { context })`

### Example Transformation:

**Before:**
```typescript
console.log(`Webhook: Processing checkout.session.completed for order ${orderId}`);
console.log(`   Payment status: ${session.payment_status}`);
console.log(`   Session ID: ${session.id}`);
```

**After:**
```typescript
logger.info('Processing checkout.session.completed', {
  orderId,
  paymentStatus: session.payment_status,
  sessionId: session.id,
});
```

## Files to Modify

- `backend/src/routes/webhooks.ts` (primary - 24 instances)
- Search other backend files for any remaining `console.log` usage and replace similarly

## Verification

- No `console.log`, `console.error`, or `console.warn` remain in webhooks.ts
- All logging uses the structured logger with appropriate levels
- Sensitive data (emails, payment amounts, session IDs) is passed as context objects, not interpolated into strings
- The application still logs all important events
