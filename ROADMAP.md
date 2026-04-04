Simplified product roadmap
=====================

## 1. Filter system (Unified attributes)

Replace all separate managers (Vendors, Colors, Specs) with a single Filter Group entity.

Group definition:

Name: Translatable (e.g., "Size" or "Material").
Type: Choose from Checkbox, Color, Text, or Dropdown.
Price Impact: Toggle to determine if the selection changes the product cost.
Product association: Each product can be linked to any number of these groups to define its characteristics and variations.

## 2. Order management

Status handling: Remove the database-driven Status Manager.
Hardcoded logic: Use fixed PHP constants (New, Processing, Shipped, etc.) for all order states. This ensures a stable, non-deletable workflow and reduces database queries.

## 3. Eliminated features

No Vendors: Remove the dedicated vendor/brand entity.
No Colors/Specs: Consolidated into the Filter system.
No Attribute sets: Simplified to direct Group-to-Product attachment.