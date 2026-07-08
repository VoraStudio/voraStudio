# Date Range Field Type Specification

## Functional Requirements

### ADDED Requirement: `date_range` field type

The system MUST support a new field type `date_range` that stores a single JSON object containing `start` and `end` datetime values.

#### Scenario: Admin creates a field definition with `date_range`

- GIVEN the admin is creating a field definition for a content type
- WHEN they select `date_range` as the field type
- THEN the system accepts and persists the field definition

#### Scenario: Admin creates an entry with a date range

- GIVEN a content type has a `date_range` field
- WHEN the admin submits start and end `datetime-local` values
- THEN the system stores them as `{"start":"2024-01-01T10:00","end":"2024-12-31T18:00"}`

#### Scenario: API serializes a date range

- GIVEN an entry has a `date_range` value stored as JSON
- WHEN the API serializes the entry
- THEN the response exposes the value as a parsed object `{ "start": "...", "end": "..." }`

### MODIFIED Requirement: `date` field type behavior

The system MUST keep the `date` field type behavior unchanged.

(Previously: `date` and `datetime` both used `<input type="datetime-local">`.)

#### Scenario: Creating an entry with a `date` field

- GIVEN a content type has a `date` field
- WHEN the admin creates or edits an entry
- THEN the form renders one `datetime-local` input and stores the value as a plain ISO string

### REMOVED Requirement: `datetime` field type

The system MUST NOT accept `datetime` as a valid field type.

(Reason: `datetime` is redundant because `date` already provides the same `datetime-local` input and storage behavior.)
(Migration: None. Existing records with `datetime` values become orphaned and are not migrated.)

#### Scenario: Existing `datetime` field is rejected

- GIVEN a field definition still has type `datetime`
- WHEN the system validates the content type
- THEN validation fails because `datetime` is no longer in `FieldDefinition::getTypes()`

## Scenarios

### Happy Path: End-to-end date range creation

- GIVEN a content type defines a required `date_range` field
- WHEN the admin fills both start and end inputs and saves the entry
- THEN the value is stored as JSON, serialized as an object by the API, and rendered as a formatted range in show and preview templates

### Edge Case: Only one side of the range is provided

- GIVEN a `date_range` field is not required
- WHEN the admin provides only `start` or only `end`
- THEN the system stores the partial object `{"start":"...","end":""}` or `{"start":"","end":"..."}` without validation errors

### Edge Case: End date is before start date

- GIVEN a `date_range` field has both values filled
- WHEN the `end` datetime is earlier than the `start` datetime
- THEN the system rejects the submission with a validation error

### Edge Case: Malformed JSON is already stored

- GIVEN a `date_range` value in the database is not valid JSON
- WHEN the API serializes the entry
- THEN the API returns the raw string value instead of crashing

## Validations

- `date_range` MUST contain `start` and `end` keys when non-empty.
- Each value MUST match the `datetime-local` format `YYYY-MM-DDTHH:mm` or be empty.
- `end` MUST be greater than or equal to `start` when both are present.
- `datetime` MUST be rejected by `FieldDefinition::getTypes()` and Symfony `Choice` validation.

## Business Rules

- `date` remains unchanged: single `<input type="datetime-local">`, stored as plain ISO string.
- `date_range` renders two `<input type="datetime-local">` inputs labeled `start` and `end`.
- `date_range` values are stored as JSON strings in the `field_values.value` column.
- Existing records with `datetime` values are not migrated; they remain readable but the field type is no longer selectable.
- No database migration is provided for orphaned `datetime` records.

## Acceptance Criteria

- [ ] `TYPE_DATETIME` constant is removed from `FieldDefinition`.
- [ ] `TYPE_DATE_RANGE` constant is added to `FieldDefinition`.
- [ ] `FieldDefinition::getTypes()` returns `date_range` and does not return `datetime`.
- [ ] `EntrySerializer` parses `date_range` JSON and returns an object; the `datetime` case is removed.
- [ ] `EntryController::resolveFieldValue()` encodes `start`/`end` POST parameters into JSON for `date_range` fields.
- [ ] `new.html.twig` renders two `datetime-local` inputs for `date_range` and removes the `datetime` block.
- [ ] `edit.html.twig` pre-fills both `date_range` inputs from stored JSON and removes the `datetime` block.
- [ ] `show.html.twig` displays `date_range` start and end formatted and removes the `datetime` block.
- [ ] `preview_generic.html.twig` displays the `date_range` as a formatted range and removes `datetime` handling.
- [ ] `api-guide.html.twig` lists `date_range` in the field types table and removes `datetime` examples.
