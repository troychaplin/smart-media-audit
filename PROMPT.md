Act as an expert WordPress core and plugin architect. I am running a local development environment via WordPress Studio and evaluating AI capabilities. 

I want to build a WordPress plugin named "Media Usage Scanner" that audits the Media Library to identify where attachments are used across the site. However, we must complete a **Planning and Architecture Phase** before any code is written. 

Do not write any plugin code or generate file artifacts yet.

### Step 1: Technical Architecture Document

Please provide a detailed technical breakdown addressing the following requirements:

1. **Database & Performance Strategy:** Media libraries can be massive. Standard SQL `LIKE` queries on every page load will crash large sites. Detail your approach for efficient, non-blocking scanning. Will you use a custom index table, background processing (WP-Cron / Action Scheduler), or a REST API-driven batching mechanism? Explain the database impact.

2. **Gutenberg & Core Parsing Strategy:** Explain exactly how your parsing logic will reliably detect media usage across:
   - Classic content strings (standard `<img>` tags and shortcodes).
   - Modern Gutenberg blocks (parsing `<!-- wp:image {"id":123} -->` comment delimiters and block attributes using native core functions like `parse_blocks`).
   - Post Featured Images (`_thumbnail_id` meta).
   - Third-party data (e.g., handling or accounting for serialized arrays in postmeta).

3. **Admin UI & UX Architecture:** Outline the structural layout for the admin view (under 'Media'). How will you handle pagination, state management for the "All/Used/Unused" filters, and real-time scanning progress bars without breaking the native WordPress admin experience?

4. **Edge Cases & Limitations:** List the technical edge cases your proposed strategy will successfully capture vs. the specific edge cases it will inherently miss (e.g., CSS background images, theme-defined choices, widgets).

### Step 2: Next Steps

Conclude your response by listing the file structure you *propose* to create once authorized, and ask me if you should proceed to the implementation phase or if any architectural adjustments are needed.