# Developer's System Map

Technical reference for where to find and edit code per functional module in this Laravel application.

## Core Route Files

- `routes/web.php`: Main application routes (public, admin, employer, jobseeker).
- `routes/auth.php`: Authentication and account security routes.

## 1) Authentication & Access Control

Description: Login/register, password reset, email verification, Google OAuth, and account-level profile/security endpoints.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (`create`, `store`, `destroy`), `RegisteredUserController.php` (`create`, `store`), `PasswordResetLinkController.php` (`create`, `store`), `NewPasswordController.php` (`create`, `store`), `VerifyEmailController.php` (`__invoke`), `EmailVerificationPromptController.php` (`__invoke`), `EmailVerificationNotificationController.php` (`store`), `ConfirmablePasswordController.php` (`show`, `store`), `PasswordController.php` (`update`), `GoogleController.php` (`redirect`, `callback`) |
| Models (Relationships/Traits) | `app/Models/User.php` (traits: `HasFactory`, `Notifiable`; relationships: `employer()`, `jobseeker()`) |
| Views | `resources/views/auth/login.blade.php`, `register.blade.php`, `forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`, `confirm-password.blade.php` |
| Routes | `routes/auth.php` (`register`, `login`, `logout`, `password.*`, `verification.*`, `auth.google.*`) |
| Database | `database/migrations/0001_01_01_000000_create_users_table.php`, `2026_01_19_000012_add_status_to_users_table.php`, `2026_01_26_000005_add_google_fields_to_users_table.php`; factory: `database/factories/UserFactory.php` |
| Extra Logic | Request: `app/Http/Requests/Auth/LoginRequest.php`; middleware used in route groups: `app/Http/Middleware/EnsureUserIsActive.php`, `RoleMiddleware.php`, `AuthenticateAny.php`, `EnsureNotInMaintenance.php`, `CheckSessionTimeout.php` |

## 2) Admin Dashboard & Governance

Description: Admin oversight for dashboard analytics, users, employers, jobseekers, documents, applications, digital IDs, reports, settings, and CMS content.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Admin/DashboardController.php` (`index`), `UserController.php` (`index`, `toggleStatus`, `updateRole`, `resetPassword`), `EmployerController.php` (`index`, `approve`, `suspend`, `activate`), `JobseekerController.php` (`index`, `suspend`, `activate`), `ApplicationsController.php` (`index`), `DocumentReviewController.php` (`index`, `approve`, `reject`), `DigitalIdController.php` (`index`, `show`, `revoke`), `ReportsController.php` (`index`, `applicationsCsv`, `usersCsv`, `hiringCsv`), `SettingsController.php` (`index`, `update`, `toggleMaintenance`), `CmsController.php` (`index`, `update`), `PagesController.php` (fallback listing pages) |
| Models (Relationships/Traits) | `app/Models/User.php`, `Employer.php`, `Jobseeker.php`, `JobPost.php`, `Application.php`, `Document.php`, `DigitalId.php`, `Setting.php` |
| Views | `resources/views/dashboards/admin.blade.php`, `resources/views/admin/users/index.blade.php`, `employers/index.blade.php`, `jobseekers/index.blade.php`, `job-posts/index.blade.php`, `applications/index.blade.php`, `documents/index.blade.php`, `digital-ids/index.blade.php`, `digital-ids/show.blade.php`, `reports/index.blade.php`, `settings/index.blade.php`, `cms/index.blade.php`, `cms/_social-icon.blade.php` |
| Routes | `routes/web.php` admin-prefixed group (`name: admin.*`) |
| Database | Core tables above plus `database/migrations/2026_01_19_000013_create_settings_table.php` |
| Extra Logic | Request: `app/Http/Requests/Admin/ReviewDocumentRequest.php`; notification: `app/Notifications/AdminPasswordReset.php` |

## 3) Employer Job Posts

Description: Employer CRUD for job postings, including publish/close/duplicate and AI/hybrid matching hooks.

| Area | Where to Edit |
|---|---|
| Controller (CRUD + actions) | `app/Http/Controllers/Employer/JobPostController.php` (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `publish`, `close`, `duplicate`) |
| Models (Relationships/Traits) | `app/Models/JobPost.php` (traits: `HasFactory`, `SoftDeletes`; relationships: `employer()`, `applications()`, `requiredSkills()`, `jobMatches()`), `app/Models/JobPostSkill.php` (`jobPost()`) |
| Views | `resources/views/employer/job-posts/index.blade.php`, `create.blade.php`, `show.blade.php`, `edit.blade.php`, `partials/share-icon.blade.php` |
| Routes | `routes/web.php` employer group: `employer.job-posts.*` |
| Database | `database/migrations/2026_01_19_000004_create_job_posts_table.php`, `2026_02_14_000001_add_benefits_and_salary_type_to_job_posts_table.php`, `2026_02_14_000002_create_job_post_skills_table.php`; factory: `database/factories/JobPostFactory.php` |
| Extra Logic | Requests: `app/Http/Requests/Employer/StoreJobPostRequest.php`, `UpdateJobPostRequest.php`; services: `app/Services/HybridJobMatcher.php`, `CandidateMatchingService.php`, `OpenAiEmbeddingService.php`, `HuggingFaceEmbeddingService.php`; model support: `app/Models/JobMatch.php` |

## 4) Public Jobs & Company Profile

Description: Public-facing job details and company profile pages.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/PublicJobController.php` (`show`), `CompanyController.php` (`show`) |
| Models (Relationships/Traits) | `app/Models/JobPost.php`, `Employer.php`, `CompanyProfile.php` (`employer()`) |
| Views | `resources/views/jobs/public-show.blade.php`, `resources/views/company/show.blade.php` |
| Routes | `routes/web.php`: `jobs.public.show`, `company.show` |
| Database | `database/migrations/2026_02_11_064846_create_company_profiles_table.php`, `2026_01_19_000002_create_employers_table.php`, `2026_02_10_000001_add_company_logo_to_employers_table.php` |
| Extra Logic | Shared layout/components in `resources/views/layouts/public.blade.php` and `resources/views/components/ui/*` |

## 5) Employer Applicants & ATS

Description: Application pipeline list/detail, status transitions, interview metadata, employer notes, import/export/template.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Employer/ApplicantsController.php` (`index`, `show`, `downloadTemplate`, `import`, `export`), `AtsController.php` (`index`, `updateStatus`), `NoteController.php` (`store`, `update`, `destroy`) |
| Models (Relationships/Traits) | `app/Models/Application.php` (trait: `SoftDeletes`; relationships: `jobPost()`, `jobseeker()`, `statuses()`, `notes()`), `ApplicationStatus.php` (`application()`, `setBy()`), `EmployerNote.php` (`employer()`, `application()`, `creator()`) |
| Views | `resources/views/employer/applicants/index.blade.php`, `show.blade.php`, `resources/views/employer/ats/index.blade.php` |
| Routes | `routes/web.php`: `employer.applicants*`, `employer.ats`, `employer.ats.status`, `employer.applicants.notes.*` |
| Database | `database/migrations/2026_01_19_000005_create_applications_table.php`, `2026_02_13_100000_add_cover_letter_file_to_applications_table.php`, `2026_01_19_000006_create_application_statuses_table.php`, `2026_01_28_000001_add_interview_fields_to_application_statuses_table.php`, `2026_02_23_155121_rename_application_status_values.php`, `2026_02_23_160522_rename_closed_status_to_for_pooling.php`, `2026_01_19_000009_create_employer_notes_table.php` |
| Extra Logic | Notifications: `app/Notifications/ApplicationStatusUpdated.php`, `ApplicationSubmitted.php`; status rename migrations affect ATS workflow values |

## 6) Jobseeker Job Discovery & Apply

Description: Job listing, matching-enhanced recommendations, job detail, and application submission.

| Area | Where to Edit |
|---|---|
| Controller | `app/Http/Controllers/Jobseeker/JobController.php` (`index`, `show`, `apply`) |
| Models (Relationships/Traits) | `app/Models/JobPost.php`, `Application.php`, `ApplicationStatus.php`, `JobMatch.php` (`jobPost()`, `jobseeker()`) |
| Views | `resources/views/jobseeker/jobs/index.blade.php`, `show.blade.php`, `partials/listing.blade.php` |
| Routes | `routes/web.php`: `jobseeker.jobs`, `jobseeker.jobs.show`, `jobseeker.jobs.apply` |
| Database | Job/Application/Match migrations above + `database/migrations/2026_02_23_030432_create_job_matches_table.php` |
| Extra Logic | Request: `app/Http/Requests/Jobseeker/ApplyJobRequest.php`; services: `app/Services/HybridJobMatcher.php`; notification: `app/Notifications/ApplicationSubmitted.php` |

## 7) Jobseeker Profile, Resume Data & Dashboard

Description: Jobseeker profile viewing/editing, structured education/experience/skills, and dashboard summaries.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Jobseeker/ProfileController.php` (`show`, `edit`, `update`), `Jobseeker/DashboardController.php` (`index`) |
| Models (Relationships/Traits) | `app/Models/Jobseeker.php` (trait: `SoftDeletes`; relationships: `user()`, `applications()`, `documents()`, `digitalIds()`, `educations()`, `workExperiences()`, `skillsList()`, `jobMatches()`, `virtualEventRegistrations()`, `registeredVirtualEvents()`), `JobseekerEducation.php`, `JobseekerWorkExperience.php`, `JobseekerSkill.php` |
| Views | `resources/views/jobseeker/profile/show.blade.php`, `edit.blade.php`, `resources/views/dashboards/jobseeker.blade.php` |
| Routes | `routes/web.php`: `jobseeker.profile.show`, `jobseeker.profile.edit`, `jobseeker.profile.update`, `jobseeker.dashboard` |
| Database | `database/migrations/2026_01_19_000003_create_jobseekers_table.php` plus profile extensions: `2026_01_26_000001_add_resume_details_to_jobseekers_table.php`, `2026_01_26_000002_add_barangay_to_jobseekers_table.php`, `2026_01_26_000003_add_region_to_jobseekers_table.php`, `2026_02_04_064628_add_name_fields_to_jobseekers_table.php`, `2026_02_04_064759_migrate_existing_names_to_jobseeker_fields.php`, `2026_02_04_090607_add_educational_attainment_to_jobseekers_table.php`, `2026_02_13_000001_add_work_experience_1_current_or_recent_to_jobseekers_table.php`, `2026_02_03_092344_create_jobseeker_education_table.php`, `2026_02_03_092344_create_jobseeker_work_experience_table.php`, `2026_02_03_092729_migrate_existing_education_experience_data.php`, `2026_02_03_092812_remove_old_education_experience_columns_from_jobseekers.php`, `2026_02_14_000001_create_jobseeker_skills_table.php`, `2026_02_14_000003_migrate_jobseeker_skills_from_text.php` |
| Extra Logic | Request: `app/Http/Requests/JobseekerProfileUpdateRequest.php`; related partials: `resources/views/profile/partials/jobseeker-resume-form.blade.php` |

## 8) Jobseeker Documents & Admin/Employer Review Loop

Description: Jobseeker uploads and document lifecycle with employer update requests and admin approval/rejection.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Jobseeker/DocumentController.php` (`index`, `store`, `storeBatch`, `show`, `download`, `destroy`), `Employer/DocumentController.php` (`index`, `show`, `requestUpdate`), `Admin/DocumentReviewController.php` (`index`, `approve`, `reject`) |
| Model (Relationships/Traits) | `app/Models/Document.php` (trait: `SoftDeletes`; relationships: `jobseeker()`, `reviewer()`) |
| Views | `resources/views/jobseeker/documents/index.blade.php`, `show.blade.php`, `resources/views/employer/documents/index.blade.php`, `show.blade.php`, `resources/views/admin/documents/index.blade.php` |
| Routes | `routes/web.php`: `jobseeker.documents*`, `employer.documents*`, `admin.documents` |
| Database | `database/migrations/2026_01_19_000007_create_documents_table.php` |
| Extra Logic | Requests: `app/Http/Requests/Jobseeker/StoreDocumentRequest.php`, `StoreDocumentsBatchRequest.php`, `app/Http/Requests/Employer/RequestDocumentUpdateRequest.php`, `app/Http/Requests/Admin/ReviewDocumentRequest.php`; notifications: `app/Notifications/DocumentUpdated.php`, `DocumentUpdateRequested.php` |

## 9) Digital ID (Issue, Preview, Verify)

Description: Employer issues digital IDs, jobseeker views own ID, admin audits/revokes, and public verification endpoint.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Employer/DigitalIdController.php` (`index`, `store`, `show`, `toggle`), `Jobseeker/DigitalIdController.php` (`updatePhoto`), `Admin/DigitalIdController.php` (`index`, `show`, `revoke`), `DigitalIdVerificationController.php` (`show`, `download`), `Jobseeker/PagesController.php` (`digitalId`) |
| Model (Relationships/Traits) | `app/Models/DigitalId.php` (trait: `SoftDeletes`; relationships: `jobseeker()`, `employer()`, `jobPost()`, `issuer()`) |
| Views | `resources/views/employer/digital-ids/index.blade.php`, `show.blade.php`, `resources/views/jobseeker/digital-id/index.blade.php`, `resources/views/admin/digital-ids/index.blade.php`, `show.blade.php`, `resources/views/digital-ids/verify.blade.php` |
| Routes | `routes/web.php`: `employer.digital-ids*`, `jobseeker.digital-id`, `jobseeker.digital-id.photo`, `admin.digital-ids*`, `digital-ids.verify`, `digital-ids.verify.documents.download` |
| Database | `database/migrations/2026_01_19_000008_create_digital_ids_table.php`, `2026_01_19_000010_add_photo_to_digital_ids_table.php`, `2026_01_26_000004_add_public_token_to_digital_ids_table.php` |
| Extra Logic | Requests: `app/Http/Requests/Employer/StoreDigitalIdRequest.php`, `app/Http/Requests/Jobseeker/UpdateDigitalIdPhotoRequest.php` |

## 10) Virtual Events

Description: Employer event CRUD/cancel and jobseeker registration/attendance views.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Employer/VirtualEventController.php` (`index`, `create`, `store`, `show`, `edit`, `update`, `cancel`, `destroy`), `Jobseeker/VirtualEventController.php` (`index`, `show`, `register`) |
| Models (Relationships/Traits) | `app/Models/VirtualEvent.php` (trait: `SoftDeletes`; relationships: `employer()`, `registrations()`, `registeredJobseekers()`; helpers: `isUpcoming`, `isOngoing`, `isMeetingLinkAvailable`, `canRegister`), `VirtualEventRegistration.php` (`virtualEvent()`, `jobseeker()`) |
| Views | `resources/views/employer/virtual-events/index.blade.php`, `create.blade.php`, `show.blade.php`, `edit.blade.php`, `resources/views/jobseeker/virtual-events/index.blade.php`, `show.blade.php` |
| Routes | `routes/web.php`: `employer.virtual-events.*`, `employer.virtual-events.cancel`, `jobseeker.virtual-events.*` |
| Database | `database/migrations/2026_02_19_064555_create_virtual_events_table.php`, `2026_02_19_064604_create_virtual_event_registrations_table.php` |
| Extra Logic | Notification: `app/Notifications/VirtualEventCreated.php` |

## 11) Employer Jobseeker Directory

Description: Employer-side searchable directory of jobseekers with template download/import/export.

| Area | Where to Edit |
|---|---|
| Controller | `app/Http/Controllers/Employer/JobseekerDirectoryController.php` (`index`, `show`, `downloadTemplate`, `import`, `export`) |
| Models (Relationships/Traits) | `app/Models/Jobseeker.php`, `app/Models/User.php` |
| Views | `resources/views/employer/jobseekers/index.blade.php`, `show.blade.php` |
| Routes | `routes/web.php`: `employer.jobseekers.index`, `.show`, `.template`, `.import`, `.export` |
| Database | Uses `jobseekers` and related profile tables; seed support in `database/seeders/JobseekerDirectorySeeder.php` |
| Extra Logic | Import/export logic is in-controller; check CSV parsing in this controller first |

## 12) Employer Sub-Users (RBAC inside employer account)

Description: Employer-managed recruiter/viewer/admin sub-accounts.

| Area | Where to Edit |
|---|---|
| Controller (CRUD + actions) | `app/Http/Controllers/Employer/SubUserController.php` (`index`, `create`, `store`, `edit`, `update`, `toggleStatus`, `destroy`) |
| Model (Relationships/Traits) | `app/Models/EmployerSubUser.php` (traits: `HasFactory`, `Notifiable`; relationship: `employer()`; helper: `isActive()`) |
| Views | `resources/views/employer/sub-users/index.blade.php`, `create.blade.php`, `edit.blade.php` |
| Routes | `routes/web.php`: `employer.sub-users.*` |
| Database | `database/migrations/2026_01_27_000001_create_employer_sub_users_table.php` |
| Extra Logic | Enum: `app/Enums/EmployerSubUserRole.php`; middleware gating: `app/Http/Middleware/EnsureEmployerRole.php`, `EnsureEmployerUser.php`, `EnsureEmployerApproved.php` |

## 13) Employer Company Branding & Profile

Description: Employer company settings, logo management, and company profile maintenance.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Employer/CompanyLogoController.php` (`update`, `destroy`), `CompanyProfileController.php` (`edit`, `update`), `Employer/PagesController.php` (`companySettings`) |
| Models (Relationships/Traits) | `app/Models/Employer.php` (`companyProfile()`), `app/Models/CompanyProfile.php` (`employer()`) |
| Views | `resources/views/employer/company-settings.blade.php`, `resources/views/employer/profile/edit.blade.php`, `resources/views/profile/partials/employer-company-logo-form.blade.php` |
| Routes | `routes/web.php`: `employer.company-settings`, `employer.company-logo.update`, `employer.company-logo.destroy`, `employer.profile.edit`, `employer.profile.update` |
| Database | `database/migrations/2026_02_11_064846_create_company_profiles_table.php`, `2026_02_10_000001_add_company_logo_to_employers_table.php` |
| Extra Logic | Request: `app/Http/Requests/Employer/UpdateCompanyLogoRequest.php` |

## 14) Notifications (Employer/Jobseeker inbox)

Description: In-app notification listing and read/redirect behavior.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/Employer/NotificationController.php` (`index`, `markReadAndRedirect`), `Jobseeker/NotificationController.php` (`index`, `markRead`, `markReadAndRedirect`) |
| Models | Uses Laravel notifications via notifiable users/sub-users |
| Views | `resources/views/employer/notifications/index.blade.php`, `resources/views/jobseeker/notifications/index.blade.php` |
| Routes | `routes/web.php`: `employer.notifications*`, `jobseeker.notifications*` |
| Database | `database/migrations/2026_01_19_000011_create_notifications_table.php` |
| Extra Logic | Notification classes in `app/Notifications/*.php` |

## 15) Account Settings (Shared user settings page)

Description: Shared account profile update/deletion for authenticated active users.

| Area | Where to Edit |
|---|---|
| Controller | `app/Http/Controllers/ProfileController.php` (`accountSettings`, `edit`, `update`, `destroy`) |
| Models (Relationships/Traits) | `app/Models/User.php`, `app/Models/Jobseeker.php` |
| Views | `resources/views/profile/account-settings.blade.php`, `profile/edit.blade.php`, `profile/partials/update-profile-information-form.blade.php`, `update-password-form.blade.php`, `delete-user-form.blade.php` |
| Routes | `routes/web.php`: `profile.settings`, `profile.edit`, `profile.update`, `profile.destroy` |
| Database | User and jobseeker tables (see Modules 1 and 7) |
| Extra Logic | Request: `app/Http/Requests/ProfileUpdateRequest.php` |

## 16) Dashboard Redirect & Landing

Description: Role-based redirection and top-level landing/pending screens.

| Area | Where to Edit |
|---|---|
| Controllers | `app/Http/Controllers/DashboardRedirectController.php` (`__invoke`) |
| Views | `resources/views/welcome.blade.php`, `resources/views/employer/pending.blade.php`, dashboards in `resources/views/dashboards/*.blade.php` |
| Routes | `routes/web.php`: `/`, `dashboard`, `employer.pending` |
| Database | N/A |
| Extra Logic | Middleware orchestration in `bootstrap/app.php` + `app/Http/Middleware/*` |

## Cross-Cutting: Matching/AI Services

Description: Services used by job recommendation/scoring workflows.

- `app/Services/CandidateMatchingService.php`
- `app/Services/HybridJobMatcher.php`
- `app/Services/OpenAiEmbeddingService.php`
- `app/Services/HuggingFaceEmbeddingService.php`
- Supporting model/table: `app/Models/JobMatch.php`, `database/migrations/2026_02_23_030432_create_job_matches_table.php`

## Cross-Cutting: Middleware Map

- `app/Http/Middleware/EnsureEmployerUser.php`
- `app/Http/Middleware/EnsureEmployerApproved.php`
- `app/Http/Middleware/EnsureEmployerRole.php`
- `app/Http/Middleware/EnsureJobseekerProfile.php`
- `app/Http/Middleware/EnsureUserIsActive.php`
- `app/Http/Middleware/EnsureNotInMaintenance.php`
- `app/Http/Middleware/AuthenticateAny.php`
- `app/Http/Middleware/RoleMiddleware.php`
- `app/Http/Middleware/PreventAuthenticatedCache.php`
- `app/Http/Middleware/CheckSessionTimeout.php`

## Notes

- No policy classes are currently present in `app/Policies`.
- Most UI is Blade-based under `resources/views`; no Vue/React SPA modules were found in this codebase.
