---
status: "accepted"
date: "2025-04-09"
decision-makers: Finn Wehn
---

# Laravel Starter Kit – Vue + Inertia

## Context and Problem Statement

After selecting Laravel as the framework, a suitable starter kit was needed to quickly implement the frontend and
authentication features. The starter kit should provide a modern, maintainable, and easily extensible foundation.
The official starter kits available with Laravel 12 were considered.

## Decision Drivers

- Personal preference
- Existing experience

## Considered Options

- Laravel Breeze (Blade)
- Laravel Breeze (Vue + Inertia)
- Laravel Breeze (React + Inertia)
- (Laravel Jetstream (Inertia + Vue))

**Laravel Jetstream (Inertia + Vue) was excluded from consideration as user authentication was not a primary project
requirement.**

## Decision Outcome

**Chosen option:** **Laravel Breeze (Vue + Inertia)**, as it fulfills all functional requirements and provides a simple
data flow between the backend and frontend. Existing experience with Vue and Inertia further accelerates development.

## Consequences

**Advantages:**

- Ready-to-use authentication for both frontend and backend
- Vue's unified component structure (template, script, and style combined)
- Direct passing of server-side data to Vue components without an additional API layer, as well as routing via Inertia

**Disadvantages:**

- Slightly more complexity compared to pure Blade-based solutions

## Pros and Cons of the Options

### Laravel Breeze (Vue + Inertia)

**Advantages:**

- Modern, component-based frontend
- Direct integration between Laravel and Vue via Inertia
- Minimal overhead, only essential features included
- Built-in option for server-side rendering (SSR)

**Disadvantages:**

- Requires learning Inertia.js concepts if unfamiliar

### Laravel Breeze (React + Inertia)

**Advantages:**

- Modern technology with a large ecosystem
- Also uses Inertia for backend-frontend integration

**Disadvantages:**

- React requires more complex state management
- Limited existing experience with React

### Laravel Breeze (Blade)

**Advantages:**

- Very simple and traditional approach without JavaScript complexity
- Quick to implement, minimal additional dependencies

**Disadvantages:**

- No dynamic frontend, limited interactivity
- Less suitable for modern web applications focused on rich user experiences
