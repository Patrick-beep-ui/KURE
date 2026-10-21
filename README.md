# KURE

A rule-driven web application for managing university clinic operations, consultations, conditions, medications, inventory, and appointments.

## Overview

KURE was designed to replace fragmented manual clinic processes with a centralized application. Its distinguishing feature is a custom domain-specific language (DSL) that allows clinic rules to be expressed in terms familiar to the domain instead of embedding every policy directly in application code.

Rules can describe behavior such as which medications should be suggested for a consultation or medical condition, how medication status changes according to a staff role, and when doctors are available. The application validates these rules against clinic data before saving them.

## Core capabilities

- Student and academic-program records
- Consultation creation and condition management
- Medication inventory, status, and suggestion endpoints
- Appointment creation and student appointment history
- Doctor listing and availability lookup
- DSL editor workflow for validating and saving clinic rules
- Semantic validation against medications, conditions, users, and roles stored by the application
- Abstract syntax tree (AST) output in structured JSON
- AST image generation for inspecting parsed rules

## DSL processing flow

~~~mermaid
flowchart TD
    A[Clinic rule entered in the UI] --> B[Laravel validation endpoint]
    B --> C[Python and textX parser]
    C --> D[Structured AST]
    D --> E[Semantic validation against clinic data]
    E --> F[Persist validated rules]
    D --> G[DOT and PNG AST visualization]
~~~

The Laravel service invokes the Python parser as a separate process, decodes its structured output, validates referenced domain values, and returns the AST through the API. This keeps the grammar and parser independent from the application services while still allowing clinic data to enforce semantic constraints.

## Architecture

- **Backend:** PHP 8.2, Laravel 12, REST endpoints
- **Frontend:** React 19, Inertia.js, Vite, Tailwind CSS
- **Database:** MySQL through Laravel's data layer
- **DSL runtime:** Python, textX, JSON, Graphviz/DOT
- **Domain services:** Students, consultations, conditions, medications, appointments, users, and rules
- **Testing:** PHPUnit through Laravel's test runner

## Local development

### Prerequisites

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL
- Python with `textX`
- Graphviz if AST PNG rendering is required

### Setup

~~~bash
git clone https://github.com/Patrick-beep-ui/KURE.git
cd KURE
composer install
npm install
~~~

Create a Laravel `.env` file, configure the application and MySQL connection, and set `PYTHON_BIN` if Python is not available as `python` on your system. Then run:

~~~bash
php artisan key:generate
php artisan migrate
composer run dev
~~~

> AST image generation requires Graphviz. The current parser script contains a Windows Graphviz executable path, so update it for your local operating system if needed.

## Testing

~~~bash
composer test
~~~

## Project context

KURE was an academic software-engineering project for a university clinic. The implementation combined clinic workflow analysis, relational application services, API design, a custom grammar and parser, semantic rule validation, an in-browser rules workflow, and AST inspection.

## Data handling

Use generated or anonymized records in development. Medical, student, credential, and institutional data must not be committed to the repository.

