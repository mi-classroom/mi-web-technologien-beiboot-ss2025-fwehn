---
status: "accepted"
date: "2025-04-09"
decision-makers: Finn Wehn
---

# ExifTool for Metadata Handling

## Context and Problem Statement

The application needs to reliably extract, manage, and write image metadata—especially IPTC—from a wide range of image
formats. PHP’s native functions and GD-based libraries offer only limited and format-specific capabilities, which are
not enough for the intended use case.

## Decision Drivers

- Broad support for common image formats (JPEG, TIFF, PNG, WebP, etc.)
- Full read and write capabilities for IPTC metadata (with possible future use of EXIF and XMP)
- Stable and proven metadata processing
- Metadata handling without affecting the image content or quality

## Considered Options

- Native PHP functions (`iptcembed()`, `getimagesize()`)
- PHP Imagick extension
- Spatie Image (based on GD or ImageMagick)
- ExifTool via CLI system calls

## Decision Outcome

**Chosen option:** **ExifTool**, as it is the only solution that consistently reads and writes metadata across multiple
formats without altering image content. It supports IPTC, EXIF, and XMP metadata standards comprehensively and is widely
adopted in the industry.

## Consequences

**Advantages:**

- Reliable embedding and extraction of metadata regardless of image format
- Compatible with future metadata needs (e.g., XMP)
- Works independently of image structure and does not re-encode files

**Disadvantages:**

- Requires server-side installation as an external dependency
- CLI-based integration adds a small layer of complexity for error handling and deployment

## Pros and Cons of the Options

### Native PHP functions (`iptcembed()`, `getimagesize()`)

**Advantages:**

- Built into PHP, no additional installation is needed

**Neutral:**

- Sufficient for simple use cases with JPEG and IPTC only

**Disadvantages:**

- Limited to JPEG format
- No support for XMP or complex metadata structures

### PHP Imagick extension

**Advantages:**

- Supports a wide range of image formats
- Capable of reading and writing metadata profiles

**Disadvantages:**

- Metadata support (especially IPTC) is inconsistent and dependent on server setup
- Requires installation of a PHP extension not available on all hosting environments

### Spatie Image (based on GD or ImageMagick)

**Advantages:**

- Integrates well with Laravel and provides common image operations (e.g., format conversion, resizing)
- Uses ImageMagick or GD under the hood and fits into existing workflows

**Neutral:**

- Primarily focused on visual/image output, not metadata handling

**Disadvantages:**

- Does not support reading or writing IPTC, EXIF, or XMP metadata
- Requires a second tool (e.g., ExifTool) to fully handle metadata requirements

### ExifTool

**Advantages:**

- Comprehensive support for IPTC, EXIF, and XMP
- Proven tool widely used in professional environments
- Works with many image formats (including TIFF and WebP)

**Disadvantages:**

- Needs to be installed and accessed via system-level commands
- External process integration requires additional care in error handling
