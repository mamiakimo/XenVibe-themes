# Add Server-Side Route Protection Middleware

## Problem

The Next.js frontend at `frontend/src/middleware.ts` only handles internationalization (next-intl), with NO authentication or route protection:

```typescript
import createMiddleware from 'next-intl/middleware';

export default createMiddleware({
  locales,
  defaultLocale,
  localePrefix: 'always',
});
```

Currently, admin routes (`/admin/*`) and user-protected routes (`/dashboard/*`, `/orders/*`, etc.) are only protected client-side via the `useAuth()` hook. This means:
- Users can see the page HTML before redirect
- Client-side protection can be bypassed
- Admin pages are accessible momentarily before auth check

## Required Fix

Extend the middleware to add server-side route protection. Since the app uses Firebase Auth (client-side tokens), the middleware should check for the auth session cookie:

```typescript
import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import createIntlMiddleware from 'next-intl/middleware';

const intlMiddleware = createIntlMiddleware({
  locales,
  defaultLocale,
  localePrefix: 'always',
});

// Routes that require authentication
const protectedRoutes = ['/dashboard', '/orders', '/licenses', '/settings', '/tickets'];
const adminRoutes = ['/admin'];

export default async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;

  // Strip locale prefix for matching
  const pathWithoutLocale = pathname.replace(/^\/(en|ar)/, '') || '/';

  // Check if route needs protection
  const isProtectedRoute = protectedRoutes.some(route => pathWithoutLocale.startsWith(route));
  const isAdminRoute = adminRoutes.some(route => pathWithoutLocale.startsWith(route));

  if (isProtectedRoute || isAdminRoute) {
    // Check for Firebase session cookie or auth token
    const sessionCookie = request.cookies.get('__session')?.value;

    if (!sessionCookie) {
      // Redirect to login with return URL
      const loginUrl = new URL(`/${request.nextUrl.locale || defaultLocale}/login`, request.url);
      loginUrl.searchParams.set('redirect', pathname);
      return NextResponse.redirect(loginUrl);
    }

    // For admin routes, verify admin claim in the token
    if (isAdminRoute) {
      try {
        // Decode JWT payload (verification happens on API side)
        const payload = JSON.parse(
          Buffer.from(sessionCookie.split('.')[1], 'base64').toString()
        );
        if (!payload.admin) {
          return NextResponse.redirect(new URL(`/${request.nextUrl.locale || defaultLocale}`, request.url));
        }
      } catch {
        return NextResponse.redirect(new URL(`/${request.nextUrl.locale || defaultLocale}/login`, request.url));
      }
    }
  }

  // Continue with i18n middleware
  return intlMiddleware(request);
}

export const config = {
  matcher: [
    '/((?!api|_next/static|_next/image|favicon.ico|sitemap\\.xml|robots\\.txt|.*\\.(?:svg|png|jpg|jpeg|gif|webp|ico)$).*)',
  ],
};
```

## Additional Steps

### 1. Set Firebase session cookie on login

In the auth context, after successful login, set a session cookie:

```typescript
// In auth-context.tsx after login
const token = await user.getIdToken();
await fetch('/api/session', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ token }),
});
```

### 2. Create session API route

Create `frontend/src/app/api/session/route.ts`:

```typescript
import { cookies } from 'next/headers';
import { NextResponse } from 'next/server';

export async function POST(request: Request) {
  const { token } = await request.json();

  cookies().set('__session', token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    maxAge: 60 * 60 * 24 * 5, // 5 days
    path: '/',
  });

  return NextResponse.json({ success: true });
}

export async function DELETE() {
  cookies().delete('__session');
  return NextResponse.json({ success: true });
}
```

## Files to Modify

- `frontend/src/middleware.ts` - Add auth protection
- `frontend/src/lib/auth-context.tsx` - Set session cookie on login
- `frontend/src/app/api/session/route.ts` - New file for session management

## Verification

- Unauthenticated users are redirected to login when accessing protected routes
- Non-admin users are redirected when accessing admin routes
- Login page shows correctly with redirect parameter
- After login, user is redirected back to original page
- Logout clears the session cookie
- i18n routing still works correctly
- Public routes remain accessible without authentication
