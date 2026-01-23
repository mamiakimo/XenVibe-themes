# Add MIME Type Validation for File Uploads

## Problem

In `backend/src/routes/products/versions.ts` (lines ~83-100), the file upload validation schema only checks path traversal and directory restrictions, but does NOT validate:
- MIME type
- File extension
- File size limits

```typescript
export const createVersionSchema = z.object({
  version: z.string().min(1).max(50),
  changelog: z.string().optional(),
  filePath: z
    .string()
    .min(1)
    .refine((path) => !path.includes('..') && !path.startsWith('/') && !path.startsWith('\\'), {
      message: 'File path must be relative and cannot contain path traversal sequences',
    })
    .refine((path) => path.startsWith('uploads/products/'), {
      message: 'File path must be within uploads/products/ directory',
    }),
  fileSize: z.number().optional(),  // No max limit!
  fileHash: z.string().max(64).optional(),
});
```

## Required Fix

### 1. Add validation to the schema:

```typescript
// Allowed file extensions for XenForo addons/styles
const ALLOWED_EXTENSIONS = ['.zip', '.xml'];
const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100MB

export const createVersionSchema = z.object({
  version: z.string().min(1).max(50),
  changelog: z.string().optional(),
  filePath: z
    .string()
    .min(1)
    .refine((path) => !path.includes('..') && !path.startsWith('/') && !path.startsWith('\\'), {
      message: 'File path must be relative and cannot contain path traversal sequences',
    })
    .refine((path) => path.startsWith('uploads/products/'), {
      message: 'File path must be within uploads/products/ directory',
    })
    .refine((path) => {
      const ext = path.substring(path.lastIndexOf('.')).toLowerCase();
      return ALLOWED_EXTENSIONS.includes(ext);
    }, {
      message: `File must have one of these extensions: ${ALLOWED_EXTENSIONS.join(', ')}`,
    }),
  fileSize: z
    .number()
    .max(MAX_FILE_SIZE, { message: `File size must not exceed ${MAX_FILE_SIZE / 1024 / 1024}MB` })
    .optional(),
  fileHash: z.string().max(64).optional(),
  mimeType: z
    .enum([
      'application/zip',
      'application/x-zip-compressed',
      'application/xml',
      'text/xml',
    ])
    .optional(),
});
```

### 2. Add server-side MIME validation on actual file (if upload endpoint exists):

If there's a file upload endpoint that accepts multipart form data, add:

```typescript
import { fileTypeFromBuffer } from 'file-type';

// After receiving the file buffer
const fileType = await fileTypeFromBuffer(buffer);
if (!fileType || !['application/zip', 'application/xml'].includes(fileType.mime)) {
  return c.json({ error: 'Invalid file type. Only ZIP and XML files are allowed.' }, 400);
}
```

### 3. Add file size enforcement:

```typescript
// In the upload handler
const MAX_UPLOAD_SIZE = 100 * 1024 * 1024; // 100MB
const contentLength = parseInt(c.req.header('content-length') || '0');
if (contentLength > MAX_UPLOAD_SIZE) {
  return c.json({ error: 'File too large. Maximum size is 100MB.' }, 413);
}
```

## Files to Modify

- `backend/src/routes/products/versions.ts` - Add validation rules
- Any file upload endpoint handler - Add MIME checking
- `backend/package.json` - Add `file-type` dependency if using buffer-based MIME checking

## Verification

- Only ZIP and XML files can be uploaded
- Files exceeding 100MB are rejected
- MIME type is validated both by extension and file content (magic bytes)
- Path traversal protection still works
- Error messages are clear and user-friendly
- Existing valid uploads continue to work
