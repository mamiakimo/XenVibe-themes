# Fix XSS Vulnerability in Admin Content Preview

## Problem

In `frontend/src/app/[locale]/(admin)/admin/content/[slug]/page.tsx` (line ~437), the admin content preview modal uses `dangerouslySetInnerHTML` without sanitization:

```typescript
<div
  className="prose prose-sm sm:prose lg:prose-lg max-w-none dark:prose-invert"
  dangerouslySetInnerHTML={{ __html: form[activeTab].content }}
/>
```

The project already has `isomorphic-dompurify` installed as a dependency (package.json), and it's correctly used in the public product description page (`product-description.tsx`), but NOT in this admin preview.

## Risk

Even though this is an admin-only page, if an admin account is compromised or a malicious admin is added, unsanitized HTML content could execute arbitrary JavaScript (stored XSS).

## Required Fix

1. Import DOMPurify in the admin content page:
```typescript
import DOMPurify from 'isomorphic-dompurify';
```

2. Sanitize the content before rendering:
```typescript
<div
  className="prose prose-sm sm:prose lg:prose-lg max-w-none dark:prose-invert"
  dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(form[activeTab].content) }}
/>
```

## Files to Modify

- `frontend/src/app/[locale]/(admin)/admin/content/[slug]/page.tsx`

## Verification

- Ensure DOMPurify is imported
- Ensure all `dangerouslySetInnerHTML` usages pass content through `DOMPurify.sanitize()`
- Test that the preview still renders formatted content correctly (bold, italic, links, images)
- Test that script tags and event handlers are stripped
