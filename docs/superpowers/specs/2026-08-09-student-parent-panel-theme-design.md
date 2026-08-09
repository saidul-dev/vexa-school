# Student & Parent Panel Design Upgrade — Reuse Admin Theme

## Problem

The Admin panel recently got a visual refresh (dark navy + cyan gradient sidebar,
pill-shaped buttons, hover-lift stat cards, tinted report cards) via
`public/assets/css/admin-theme.css`, linked only from `admin/navigation.blade.php`
and `superadmin/navigation.blade.php`. The Student and Parent panels still use the
older flat styling and look visually inconsistent with Admin.

## Finding

Admin, Student, and Parent panels already share the same HTML structure and CSS
class vocabulary (`sidebar`, `nav-links`, `dashboard_ShortListItem`, `dsHeader`,
`dsBody`, `eBtn`, `eTable`, `eBadge`, `btn-primary`, etc.) from the common
`main.css` / `style.css` baseline. `admin-theme.css` (200 lines) contains no
admin-specific selectors — it only re-styles these shared class names. Admin's
`navigation.blade.php` and `dashboard.blade.php` have no inline `<style>` blocks;
all of the visual refresh lives in that one file.

This means the Admin look can be applied to Student and Parent panels by linking
the existing stylesheet — no markup changes needed.

## Approach

Add one `<link>` tag to each of:
- `resources/views/student/navigation.blade.php`
- `resources/views/parent/navigation.blade.php`

```html
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/admin-theme.css') }}" />
```

Placed in the same position as in `admin/navigation.blade.php` (after
`custom.css`). Since `navigation.blade.php` is the shared layout every
Student/Parent page `@extends`s, this applies the refreshed look to the whole
panel (dashboard, Fee Manager, Attendance, Profile, etc.) in one change.

Admin's own files are not touched — zero risk to the already-working Admin panel.

## Rejected Alternative

Renaming `admin-theme.css` to a neutral name (e.g. `theme.css`) and re-linking it
from all three navigation files. Marginally cleaner naming, but requires editing
Admin's working navigation file for no functional benefit. Rejected per YAGNI.

## Verification Plan

After the change, log in via the existing working demo credentials (Student and
Parent, confirmed working from prior session) and visually check in-browser:
- Student: dashboard, Fee Manager list, Attendance
- Parent: dashboard, Fee Manager list

Confirm sidebar gradient, buttons, and cards render like Admin's, and no layout
breaks (missing icons, overlapping elements, unreadable text/contrast).

## Out of Scope

- Any change to Admin or Superadmin panel files.
- Structural/content changes to Student or Parent pages (this is a pure CSS
  theming change).
- Teacher, Accountant, Librarian panels (not requested).
