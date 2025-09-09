Rocketsview System Development Rules & SOP

Introduction
This document outlines the best practices, standards, and rules for Laravel development at Rocketsview. It is designed to ensure consistency, maintainability, and professionalism across all projects. Use this as your primary reference for migrations, models, GitHub, database, API, documentation, and business standardization. By Faiz Nasir. 
⸻
Laravel Best Practices

Migration Standards
- Table Naming: Use snake_case, plural (e.g., locations, public_holidays).
- Pivot Tables: Snake_case, singular, alphabetical order (e.g., location_public_holiday).
- Column Naming: Snake_case, no table prefix, full form (e.g., name, job_title).
    - Date/time: [verb]_at (e.g., created_at).
    - User-related: [table_name]_id, [noun]_[table_name]_id, [verb]_by (e.g., user_id, approver_user_id).
- Primary Key: id; Foreign Key: [table_name_singular]_id (e.g., article_id).
- Migration Naming: Snake_case, create_[table_plural]_table for creation, update_[table_plural]_table for amendments.
- Align Names: Match table/column names and sequence to frontend/module fields.
- Default Columns: id, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, deleted_token, partition_created_at (if needed).
- Integer Types: Use appropriate type (e.g., tinyInteger for small values).
- Keys: Use id() and foreignId().
- Timestamps: Prefer timestamp() over dateTime().
- Avoid: float(), double() (use decimal()); char() (use varchar()).
- Partitioning: For high-growth tables (>1M rows, 10 years retention).

Model Standards
- Class Name: Singular, camel case matching table (e.g., Order, DailySalesItemReport).
- SoftDeletes: Use the trait from Illuminate\Database\Eloquent\SoftDeletes.
- Attributes: Set $attributes for deleted_token value.
- Constants: Match migration ENUMs.
- Boot Function: Set partition_created_at in creating callback.
- Serialize Dates: Override serializeDate for human-readable format.
- Relationships: Define all possible (even unused); use singular camel case; set bidirectionally except for User.
⸻
GitHub Practices

User Privileges
- Managers/Assistant Managers: Owners
- Team Leads/Senior/Junior/Intern Developers, Business Analysts: Members
- Repository Roles:
    - Project Technical Lead: Admin
    - Developer: Write
    - Business Analyst: Read
- Branch Protection:
    - Require PR, approvals, status checks, up-to-date branches, linear history, restrict pushes/bypasses, no force pushes/deletions for protected branches.

Branching & Commits
- Branch Naming:
    - Use feature/ for tasks, hotfix/ for bugs from master.
    - Format: feature/hotfix-{module}-{task-summary} (e.g., feature-homepage-announcement).
    - No mixing unrelated features.
- Commit Messages:
    - Prefix: feat: (feature), fix: (bug), style: (formatting), chore: (build/tools).
    - Example: feat: add date of birth in customer detail page.
⸻
Database & Security Practices

User Privileges
- Production:
    - Technical Lead: Full (Read/Create/Update/Delete/Truncate/Drop/Alter)
    - Developer/Business Analyst: Read only
- Staging:
    - Technical Lead: Full
    - Developer: Read/Create/Update/Delete
    - Business Analyst: Read only

Archiving
- Use the archive package for historical data.
- Move package to packages/archive, update composer.json, require package, publish assets, add archive connection, create archive classes, schedule archiving.
- Retain 180 days; archive older data.
⸻
API & Documentation Practices

API Configuration
- Use Harmony package for centralized API requests.
- Components: Base URL, Endpoint, Headers, Method, Body, Query.
- Create Harmony class, send requests via Harmony facade.

Postman Documentation
- Workspace: Project name
- Collection: {product}-API Documentation-{type}
- Folder: System sitemap
- Set Bearer Token default, URL variable for domain
- API Requests: Create/Read/Update/Delete/View All
- Responses: Successful, Malformed (422), Unauthenticated (401), Forbidden (403), Not Found
- Descriptions: Title, Description, Getting Started, Authentication, Errors, Headers, Variables

Detecting Column Changes
- Use wasChanged for post-save actions, isDirty for in-function checks.
- MySQL InnoDB locks tables during ops; Laravel tracks original/sync attributes.
⸻
Business Standardization

Document Number Format
- Format: <1 env prefix><3 system char><2 doc char><[partner prefix]><increment>
- Examples:
    - Production: HTSIV00000001
    - Staging: SHTSIV00000001
- Prefix Rules: First letter per word (e.g., SO for Sales Order)
- Components: Env (1 char), System (3 chars), Table (2 chars), Number (8 digits)
- Finance Prefixes: IV (Invoice), SO (Sales Order)
- Request Prefixes: AS (Account Sync), TU (Top Up)
- Transaction Prefixes: CD (Credit), SC (Stock)
- Actions: Apply to new tables, coordinate transitions from old formats.
 ———————————————————————————————————————————————————————————————————————————————————  WROTE BY: FAIZ NASIR

———————————————————————————————————————————————————————————————————————————————————