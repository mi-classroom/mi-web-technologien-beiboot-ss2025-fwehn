---
status: "accepted"
date: "2025-09-10"
decision-makers: Finn Wehn
---

# Persistent Storage and Management of IPTC Metadata

## Context and Problem Statement

In order to maintain a consistent application state, IPTC metadata must be stored, retrieved, and modified in a
persistent manner. The solution should allow simple access to this metadata and remain flexible enough to support
future extensions. Furthermore, different entities (e.g., images or folders) should be able to make use of the same
IPTC data model.

## Decision Drivers

- Simple and fast access to metadata
- Reusability across multiple entities

## Considered Options

- Directly written into the image file
- Extension of the existing image/folder tables
- Standalone table with polymorphic relation

## Decision Outcome

**Chosen option:** **Standalone table with polymorphic relation**, because it provides the most flexible and
maintainable solution. By centralizing IPTC data in a dedicated table, validation and management are simplified.
A positive side effect is that the model can easily be extended to other entities (such as presets) without structural
changes to existing tables.

### Consequences

**Advantages:**

- Clear separation of concerns between entities and metadata
- Only one table needs to be maintained and extended
- Easy integration with Laravel’s polymorphic relationships
- Future entities can reuse the same IPTC data structure

**Disadvantages:**

- Images need to be imported and indexed to be visible in the application and to allow metadata inspection and editing
- Images still need to be exported to write the metadata back into the image files

## Pros and Cons of the Options

### Directly written into image file

**Advantages:**

- No database integration required
- No additional indexing of images necessary
- Metadata is stored directly in the file itself

**Disadvantages:**

- Accessing and modifying metadata becomes more complex
- Folders cannot hold IPTC data

### Extension of the existing image/folder tables

**Advantages:**

- Direct integration with existing Eloquent models
- Different sets of IPTC fields can be defined per model (e.g., images vs. folders)

**Disadvantages:**

- Requires structural changes in multiple tables if extended
- Limited reusability across different entity types
- Images need to be exported to embed metadata

### Standalone Table with Polymorphic Relation

**Advantages:**

- Centralized structure for all IPTC metadata
- Reusable across different entities (images, folders, etc.)
- Easy to validate and manipulate using Eloquent

**Disadvantages:**

- Metadata persistence in image files still requires exporting
- All models must share the same set of IPTC fields (less flexibility)
