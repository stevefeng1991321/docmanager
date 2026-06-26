<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class PlanWorkReportKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'manuals')->first();

        if (!$category) {
            $this->command->warn('Manuals category not found. Run DatabaseSeeder first.');
            return;
        }

        // ── Article 1: Plan Management ────────────────────────────────────────────

        BasicKnowledgeTrend::updateOrCreate(
            ['title' => 'Plan Management: A Complete Guide'],
            [
                'category_id' => $category->id,
                'status'      => 'published',
                'tags'        => ['plan', 'management', 'tasks', 'workflow', 'collaboration'],
                'summary'     => 'Plan Management lets admins and editors create, assign, and track work plans with tasks, comments, and file attachments. This guide explains every feature from plan creation through completion, covering all roles and statuses.',
                'content'     => <<<'MD'
## Overview

Plan Management is the structured workflow for creating, assigning, tracking, and completing work plans across departments and projects. A plan is a high-level work item that contains tasks, comments, and file attachments, and is assigned to one or more employees.

Plans are accessible at two levels:
- **Admin/Editor** — full lifecycle management via the admin panel
- **Employee** — read-only visibility of plans they are assigned to, plus the ability to add comments

---

## Plan Fields

Each plan records the following information:

| Field | Description |
|---|---|
| Plan Number | Auto-generated unique ID in the format PLN-00001 |
| Title | Short descriptive name for the plan |
| Description | Full explanation of what the plan covers (optional) |
| Category | Type of plan: daily, weekly, monthly, quarterly, annual, personal, team, project, or strategic |
| Department | The department responsible (optional) |
| Project | The related project (optional) |
| Owner | The user who created the plan |
| Priority | Urgency level: low, medium, high, or critical |
| Status | Current lifecycle state (see statuses below) |
| Start Date | When work begins (optional) |
| Due Date | Deadline (optional) |
| Completion Date | Auto-filled when status changes to "Completed" |
| Estimated Hours | Expected effort in hours (optional) |
| Actual Hours | Real effort recorded (optional) |
| Progress | 0–100% calculated automatically from task completion |
| Tags | Free-form labels for searching and filtering (optional) |
| Notes | Internal notes visible to admin and assigned employees (optional) |

---

## Plan Statuses

A plan moves through the following statuses during its lifecycle:

| Status | Meaning | Badge Color |
|---|---|---|
| Draft | Plan is being prepared, not yet active | Gray |
| Pending | Plan is approved and waiting to start | Yellow |
| In Progress | Work is actively underway | Blue |
| On Hold | Work is paused temporarily | Orange |
| Completed | All work is done | Green |
| Cancelled | Plan was abandoned | Red |
| Archived | Plan is closed and stored for reference | Purple |

When a plan's status is set to **Completed**, the system automatically records the current date as the Completion Date. A plan is flagged as **overdue** when its due date has passed and its status is not Completed, Cancelled, or Archived.

---

## Plan Priorities

| Priority | Meaning | Badge Color |
|---|---|---|
| Low | Non-urgent, can be delayed | Green |
| Medium | Normal importance | Blue |
| High | Needs attention soon | Orange |
| Critical | Must be resolved immediately | Red |

---

## Plan Tasks

Each plan can contain multiple tasks. Tasks represent the individual steps required to complete the plan.

**Task Fields:**

| Field | Description |
|---|---|
| Title | Short name of the task |
| Description | Detailed explanation (optional) |
| Assigned To | The employee responsible for this task |
| Priority | low / medium / high / critical |
| Status | pending / in_progress / completed / cancelled |
| Start Date | When this task begins (optional) |
| Due Date | Task deadline (optional) |
| Completed At | Auto-filled when status changes to "Completed" |
| Notes | Additional remarks (optional) |

**Task actions available to admins:**
- Add a new task to a plan
- Update any task field
- Toggle a task between pending and completed with a single click
- Delete a task

**How progress is calculated:**
The plan's Progress field is automatically recalculated whenever a task changes status. The formula is:

Progress = (number of completed tasks / total tasks) × 100

If a plan has no tasks, progress stays at 0%.

---

## Plan Employees (Assignment)

A plan can be assigned to multiple employees. Assigned employees can:
- View the plan and all its tasks, comments, and attachments
- Add comments

Assignment is managed through a many-to-many relationship. Admins can update the assigned employees list at any time via the edit form.

Employees who are not assigned to a plan cannot see it in their client view.

---

## Plan Comments

Comments allow team communication directly on the plan record.

- Any admin, editor, or assigned employee can add a comment
- Comments show the author's name and the timestamp
- Admins can delete any comment; employees can delete their own comments
- When a non-owner adds a comment, the plan owner receives a notification

---

## Plan Attachments

Files can be uploaded directly to a plan for reference or deliverables.

- Maximum file size: **20 MB** per attachment
- Files are stored in storage/app/public/plan-attachments/{plan_id}/
- Attachment records include: original file name, file size, MIME type, and the uploader
- Admins can download or delete any attachment
- Assigned employees can download attachments but cannot delete them

---

## Plan Categories

Plans are organized by category to reflect their time horizon or scope:

| Category | Purpose |
|---|---|
| Daily | Single-day work items |
| Weekly | Week-long work cycles |
| Monthly | Month-spanning efforts |
| Quarterly | Quarter-level planning |
| Annual | Full-year strategic plans |
| Personal | Individual employee plans |
| Team | Department or team-level plans |
| Project | Plans tied to a specific project |
| Strategic | High-level organizational plans |

---

## Admin Capabilities

Admins and editors have full control over the plan lifecycle:

1. **Create** — Fill in all fields, assign employees, set priority and due date
2. **Edit** — Update any field at any time
3. **Manage tasks** — Add, edit, toggle, and delete tasks
4. **Add comments** — Communicate with assigned employees
5. **Upload attachments** — Attach files up to 20 MB
6. **Archive** — Move a completed plan to archived state via the archive action
7. **Duplicate** — Copy a plan with a new plan number; duplicated plan starts as Draft with 0% progress
8. **Delete (soft)** — Plans are soft-deleted and can be recovered if needed

Filtering on the index page: search by title or plan number; filter by status, priority, category, department, or assigned employee; filter by start date / due date range.

---

## Employee (Client) Capabilities

Employees see only plans they are assigned to.

1. **View plan details** — Title, description, status, priority, tasks, comments, attachments
2. **Add comments** — Participate in plan discussions
3. **Download attachments** — Access files uploaded by admins

Employees cannot create, edit, or delete plans.

---

## Analytics Dashboard

The Plan Analytics dashboard (admin only) provides a snapshot of plan health:

- **Summary cards**: total, active, completed, overdue, due today, draft, on hold
- **Charts**: breakdown by status, by priority, by department
- **Recent plans** list
- **Overdue plans** list with days overdue
- **Task statistics**: total tasks, completed, in progress, overdue tasks

---

## Common Workflows

**Creating and assigning a new plan:**
1. Go to Admin → Plans → Create
2. Fill in title, category, priority, start and due dates
3. Add a description and any initial notes
4. Assign employees in the Assigned Employees field
5. Save — the plan number (PLN-XXXXX) is generated automatically
6. Add tasks to break the plan into actionable steps

**Tracking progress:**
1. Open the plan detail page
2. Click the toggle button next to a task to mark it complete
3. The plan's Progress bar updates automatically
4. Change the plan status to In Progress once work begins

**Closing a plan:**
1. Ensure all tasks are completed or cancelled
2. Set status to Completed — the completion date is recorded automatically
3. Optionally archive the plan to remove it from active views

**Duplicating a plan:**
1. Open the plan detail page
2. Click Duplicate
3. A new plan is created with a new plan number, status Draft, and 0% progress
4. Edit the duplicate to adjust dates and reassign employees as needed
MD,
            ]
        );

        // ── Article 2: Work Report Management ────────────────────────────────────

        BasicKnowledgeTrend::updateOrCreate(
            ['title' => 'Work Report Management: A Complete Guide'],
            [
                'category_id' => $category->id,
                'status'      => 'published',
                'tags'        => ['work report', 'reporting', 'review', 'approval', 'workflow'],
                'summary'     => 'Work Report Management enables employees to submit daily, weekly, and monthly reports for manager or admin review. This guide covers report creation, the submission-to-approval workflow, task tracking inside reports, comment types, attachments, and the roles of employee, manager, and admin.',
                'content'     => <<<'MD'
## Overview

Work Report Management is the system for employees to document their completed work and submit it for review and approval. Reports can be daily, weekly, or monthly, and may be linked to a project. Managers and admins review submitted reports, add feedback, and approve or reject them.

---

## Report Fields

| Field | Description |
|---|---|
| Title | Short description of the reporting period |
| Type | daily / weekly / monthly |
| Report Date | The date or period start date the report covers |
| Project | Related project (optional) |
| Client Name | Name of the client involved (optional) |
| Tasks Completed | Summary of what was done during the period |
| Task Descriptions | Detailed breakdown of each task (optional) |
| Challenges | Obstacles encountered during the period (optional) |
| Solutions | How challenges were resolved (optional) |
| Notes | Any additional information (optional) |
| Work Hours | Total hours worked (optional) |
| Overall Progress | 0–100% self-assessed completion estimate (optional) |
| Status | Current stage in the review workflow (see below) |

---

## Report Statuses

Work reports follow a defined review workflow:

| Status | Meaning | Who Acts |
|---|---|---|
| Draft | Report is being written, not yet submitted | Employee |
| Submitted | Report has been sent for review | Manager / Admin |
| Under Review | Manager/admin has opened the report for active review | Manager / Admin |
| Approved | Report has been accepted | Manager / Admin |
| Rejected | Report needs revision and resubmission | Employee |

**Status transitions:**
- Employee creates → Draft
- Employee submits → Submitted (manager is notified)
- Manager/Admin opens review → Under Review
- Manager/Admin approves → Approved
- Manager/Admin rejects → Rejected (employee is notified)
- Employee revises a rejected report and resubmits → Submitted again

Only **Draft** and **Rejected** reports can be edited or deleted by the employee. Once submitted, the employee cannot modify the report content.

---

## Report Tasks

Each work report can include an inline task list to itemize the work done.

**Task fields:**

| Field | Description |
|---|---|
| Title | Name of the task |
| Status | completed / in_progress / planned |
| Priority | low / medium / high |
| Completion % | 0–100% progress for this specific task |
| Time Spent | Hours spent on this task |

Task records are part of the report and are saved together with it. There is no separate task management interface — tasks are added and edited directly in the report create/edit form.

---

## Report Comments

Comments are the primary channel for communication between employee and reviewer.

**Comment Types:**

| Type | Who Can Use | Purpose |
|---|---|---|
| Comment | All roles | General discussion or clarification |
| Feedback | Manager / Admin | Positive or constructive feedback on the report |
| Revision Request | Manager / Admin | Formal request for the employee to revise specific content |

- Employees can only post plain Comment type
- Managers and admins can post Comment, Feedback, or Revision Request
- All participants are notified when a comment is added to a report they are involved with

---

## Report Attachments

Supporting files can be attached to any report.

- Maximum file size: **10 MB** per attachment
- Files are stored in storage/app/public/work-reports/{report_id}/attachments/
- Image files (JPG, PNG, GIF, WebP, BMP) can be previewed inline
- Non-image files can be downloaded
- Employees can upload and delete their own attachments
- Reviewers can download and preview attachments but cannot delete them

---

## Roles and Permissions

### Employee (Report Owner)

An employee must have an employee profile in the system to create work reports.

| Action | Allowed When |
|---|---|
| Create a report | Always (requires employee profile) |
| View own reports | Always |
| Edit a report | Status is Draft or Rejected only |
| Delete a report | Status is Draft or Rejected only |
| Submit a report | Status is Draft or Rejected |
| Add a plain Comment | Report is visible to the employee |
| Upload attachments | Report is Draft or Rejected |
| Delete own attachments | Report is Draft or Rejected |
| Duplicate a report | Always (creates a new Draft copy) |

Employees who are also managers can additionally view their team members' reports (all statuses except Draft) under the "Team" tab on the index page.

### Manager

A manager is a user whose employee profile has the manager flag set. Managers have all standard employee permissions plus:

| Action | Allowed When |
|---|---|
| View team reports | Status is not Draft |
| Review a team report | Report is Submitted or Under Review |
| Approve / reject / mark under review | Report belongs to a direct subordinate |
| Add Feedback or Revision Request comments | Report is visible to the manager |
| Download / preview team attachments | Always |

### Admin / Editor

Admins and editors have unrestricted access across all reports.

| Action | Description |
|---|---|
| View all reports | No restriction on employee or department |
| Filter and search | By employee, department, manager, project, type, status, date range |
| Review any report | Approve, reject, or mark under review |
| Add any comment type | Comment, Feedback, or Revision Request |
| Export reports to CSV | Via the Analytics page |
| View analytics dashboard | Full breakdown by status, department, project, and contributor |

---

## Analytics Dashboard (Admin Only)

The Work Report Analytics dashboard provides organisation-wide visibility:

**Summary counts:** total reports; by status (draft, submitted, under review, approved, rejected); by type (daily, weekly, monthly).

**Submission metrics:** reports submitted today, this week, and this month.

**Department statistics:** number of reports per department; total hours reported per department.

**Project statistics:** reports linked per project; average overall progress per project.

**Top contributors (Top 10):** ranked by report count, total hours reported, and average overall progress.

**Export:** Admins can export a filtered set of reports to CSV for use in spreadsheets or payroll tools.

---

## Duplicate Feature

Any report can be duplicated regardless of status. The duplicate:
- Creates a new **Draft** report
- Copies the title, type, project, client name, and all text fields
- Copies all inline tasks (reset to their original status)
- Does **not** copy attachments or comments
- Is owned by the same employee as the original

This is useful for recurring reports (e.g., weekly reports that repeat similar tasks).

---

## Common Workflows

**Submitting a new daily report:**
1. Go to Work Reports → Create
2. Select Type: Daily, set the Report Date to today
3. Fill in Tasks Completed and any optional fields
4. Add inline tasks if you want to itemize work
5. Click Save as Draft to save without submitting, or Submit to send immediately
6. Your manager receives a notification when you submit

**Revising a rejected report:**
1. Open the report (status shows Rejected)
2. Read the reviewer's Revision Request comment for guidance
3. Click Edit — only available on Draft and Rejected reports
4. Make the required changes
5. Click Submit — status returns to Submitted and the manager is notified again

**Reviewing and approving a team report (manager):**
1. Go to Work Reports — switch to the Team tab
2. Open the report you want to review
3. Read the tasks completed, challenges, and notes
4. Optionally add a Feedback or Revision Request comment
5. Click Approve (or Reject with a required comment)
6. The employee is notified of the decision

**Exporting reports for payroll or auditing (admin):**
1. Go to Admin → Work Report Analytics
2. Set the date range, department, and status filters as needed
3. Click Export CSV
4. Open the downloaded file in Excel or any spreadsheet application
MD,
            ]
        );

        // ── Article 3: Plan Management vs Work Report Management ─────────────────

        BasicKnowledgeTrend::updateOrCreate(
            ['title' => 'Plan Management vs Work Report Management: Key Differences'],
            [
                'category_id' => $category->id,
                'status'      => 'published',
                'tags'        => ['plan', 'work report', 'comparison', 'workflow', 'management'],
                'summary'     => 'Plan Management and Work Report Management look similar at first glance — both involve tasks, comments, and files. This article explains exactly how they differ in purpose, direction, ownership, workflow, and when to use each one.',
                'content'     => <<<'MD'
## Introduction

Plan Management and Work Report Management are two distinct systems that serve opposite ends of the work lifecycle. Plans define and assign future work; work reports document and review completed work. Understanding the difference between them helps every team member know which tool to use and why.

---

## The Core Difference in One Sentence

**A plan answers "What needs to be done, by whom, and by when?"**
**A work report answers "What did I do, and did management approve it?"**

---

## Direction of Information Flow

The most fundamental structural difference is the direction each system flows:

| | Plan Management | Work Report Management |
|---|---|---|
| **Initiated by** | Admin / Editor | Employee |
| **Flows toward** | Employees (assigned by management) | Management (submitted for review) |
| **Direction** | Top-down | Bottom-up |

Plans are pushed down from management to employees. Work reports are pushed up from employees to management.

---

## Purpose and Timing

| | Plan Management | Work Report Management |
|---|---|---|
| **Purpose** | Assign and coordinate future work | Document and verify completed work |
| **Time orientation** | Forward-looking (what to do) | Backward-looking (what was done) |
| **Scope** | Can span days, months, or years | Covers a specific past period (day/week/month) |
| **Output** | A structured work plan with tasks and deadlines | A record of effort and output for approval |

---

## Ownership and Who Can Create

| | Plan Management | Work Report Management |
|---|---|---|
| **Who creates it** | Admin or Editor only | Any employee with an employee profile |
| **Who owns it** | The admin who created it | The employee who wrote it |
| **Who can edit it** | Admin / Editor at any time | Only the employee, and only when status is Draft or Rejected |
| **Assigned employees** | Can view and comment, but not modify | Not applicable — reports are personal documents |

---

## Status Workflows Compared

The two systems have different status models because they serve different review purposes.

**Plan statuses** reflect the work execution state:

Draft → Pending → In Progress → On Hold → Completed → Archived
                                         → Cancelled

Plans do not require formal approval. Management controls the status directly.

**Work Report statuses** reflect the review and approval state:

Draft → Submitted → Under Review → Approved
                                 → Rejected → (employee revises) → Submitted again

Work reports require formal sign-off. Employees cannot move their own reports past "Submitted".

---

## Tasks: Managed vs Logged

Both systems include tasks, but they serve entirely different purposes.

| | Plan Tasks | Work Report Tasks |
|---|---|---|
| **Who creates tasks** | Admin / Editor | The employee themselves |
| **Purpose** | Break the plan into actionable steps to be completed | Log what was actually worked on during the reporting period |
| **Assignment** | Each task can be assigned to a specific employee | Tasks belong to the report; no separate assignment |
| **Progress impact** | Task completion automatically updates plan progress (%) | No automatic effect; report progress is self-reported |
| **Toggle** | Admins can toggle tasks complete/pending with one click | Tasks are saved as part of the report form |
| **Statuses** | pending / in_progress / completed / cancelled | completed / in_progress / planned |
| **Extra fields** | Due date, start date, notes, assigned employee | Completion %, time spent in hours, priority |

**Key insight:** Plan tasks are instructions given to employees. Work report tasks are evidence provided by employees.

---

## Progress Tracking

| | Plan Management | Work Report Management |
|---|---|---|
| **Progress source** | Calculated automatically from task completion | Self-reported by the employee (Overall Progress field) |
| **Formula** | (completed tasks ÷ total tasks) × 100 | Manual 0–100% estimate entered by the employee |
| **Reliability** | Objective — reflects actual task status in the system | Subjective — relies on the employee's own assessment |
| **Overdue detection** | System flags overdue plans automatically (due date passed, not completed) | No automatic overdue flag; managers judge timeliness by submission date |

---

## Comments and Feedback

Both systems allow comments, but work reports have a richer comment model because they go through a formal review.

| | Plan Comments | Work Report Comments |
|---|---|---|
| **Types available** | Single type (general comment) | Comment / Feedback / Revision Request |
| **Who can comment** | Admin, editor, assigned employees | Employee (comment only); Manager/Admin (all types) |
| **Formal review tool** | No — comments are informational only | Yes — Revision Request is a formal action that tells the employee what to change |
| **Notification on comment** | Plan owner is notified when others comment | Both employee and reviewer are notified depending on context |

---

## Attachments

| | Plan Attachments | Work Report Attachments |
|---|---|---|
| **Maximum file size** | 20 MB | 10 MB |
| **Who uploads** | Admin / Editor | Employee |
| **Who can delete** | Admin / Editor | Employee (own attachments, Draft/Rejected reports only) |
| **Image preview** | No inline preview | Yes — images can be previewed directly in the browser |
| **Storage path** | plan-attachments/{plan_id}/ | work-reports/{report_id}/attachments/ |

Plan attachments are typically references and deliverables from management. Work report attachments are supporting evidence from the employee (screenshots, timesheets, completed documents).

---

## Visibility Rules

| | Plan Management | Work Report Management |
|---|---|---|
| **Employee sees** | Only plans they are explicitly assigned to | Only their own reports |
| **Manager sees** | All plans (in admin view) | Own reports + team members' non-draft reports |
| **Admin sees** | All plans | All reports |
| **Unassigned employee** | Cannot see the plan at all | Not applicable |

---

## Duplication Behaviour

| | Plan Duplication | Report Duplication |
|---|---|---|
| **Who can duplicate** | Admin / Editor | Any employee (own reports) |
| **What carries over** | All plan fields, no tasks | Title, type, text fields, inline tasks |
| **What resets** | Plan number (new), status (Draft), progress (0%), actual hours | Status (Draft), not submitted/reviewed dates |
| **Common use case** | Recurring project structures; template plans | Weekly/monthly reports with similar tasks each period |

---

## Analytics and Reporting

| | Plan Analytics | Work Report Analytics |
|---|---|---|
| **Primary focus** | Work execution health (overdue, progress, status distribution) | Workforce productivity (hours, submission frequency, approval rate) |
| **Overdue tracking** | Yes — automatic flag; shown in analytics | No automatic overdue flag |
| **Department view** | Plans per department, status breakdown | Hours and report count per department |
| **Export** | Not available | CSV export of filtered reports |
| **Top contributors** | Not tracked | Top 10 employees by count, hours, and progress |

---

## When to Use Each

| Situation | Use |
|---|---|
| You need to assign a piece of work to an employee | Plan Management |
| You need to track progress of an ongoing project | Plan Management |
| You want to set deadlines and priorities for a team | Plan Management |
| You need an employee to document what they did this week | Work Report Management |
| You need to formally approve or reject someone's submitted work | Work Report Management |
| You want to record hours worked and send them for payroll review | Work Report Management |
| You need both — assign the work, then verify it was completed | Create a Plan first, then require a Work Report when the period ends |

---

## Quick Reference

| Dimension | Plan | Work Report |
|---|---|---|
| Created by | Admin / Editor | Employee |
| Time orientation | Future | Past |
| Needs approval | No | Yes |
| Tasks managed by | Admin | Employee |
| Progress calculation | Automatic | Manual |
| Comment types | 1 (general) | 3 (comment, feedback, revision request) |
| Max attachment size | 20 MB | 10 MB |
| Duplicate available | Admin only | Employee |
| Analytics export | No | Yes (CSV) |
| Visibility control | Explicit assignment | Owner + reviewer chain |
MD,
            ]
        );

        $this->command->info('Seeded 3 BasicKnowledgeTrend entries: Plan Management, Work Report Management, and their comparison.');
    }
}
