---
status: "accepted"
date: "2025-04-09"
decision-makers: Finn Wehn
---

# PHP + Laravel

## Context and Problem Statement

For the development of a new application, a programming language and ideally a framework was needed that offered easy
database integration, API functionalities, image management, and openness for the integration of external tools or
plugins.

## Decision Drivers

- Simple image processing
- Fast development start
- Expandability through a large ecosystem of external packages
- Personal preference

## Considered Options

- PHP + Laravel
- JavaScript + Nuxt
- Python + Django
- Go (with Gin or Fiber)

## Decision Outcome

**Chosen option:** **PHP + Laravel**, because it meets all functional requirements, leverages existing knowledge, and
enables a quick project start. Laravel provides a complete framework with built-in solutions for authentication,
routing,
database integration, and API development. The documentation is extensive and the community is large, which further
facilitates development.

## Consequences

**Advantages:**

- Quick setup and development start thanks to Laravel starter kits
- Full support for backend, API, and database integration
- Existing experience with both PHP and Laravel
- Large availability of extensions and plugins

**Disadvantages:**

- The PHP GD library does not support exporting TIFF files
- Editing IPTC metadata is not directly possible with GD

## Pros and Cons of the Options

### PHP + Laravel

**Advantages:**

- Broad support for databases, APIs, and image processing
- Extensive ecosystem and many expansion options
- Fast development cycles possible

**Disadvantages:**

- Limited image processing capabilities (no native TIFF or IPTC support)

### JavaScript + Nuxt

**Advantages:**

- Single language for both frontend and backend
- Modern and popular technology

**Disadvantages:**

- Separate backend implementation required for full functionality
- More complex configuration for database integration and image processing

### Python + Django

**Advantages:**

- Very stable and proven for web development
- Automatically generated admin interface facilitates management

**Disadvantages:**

- Fewer native solutions for image processing compared to PHP
- Python

### Go (Gin/Fiber)

**Advantages:**

- Very high performance for APIs
- Low resource consumption

**Disadvantages:**

- Less comfortable frameworks for web development
- Extensive image processing requires additional libraries
- No existing experience
