# Homepage programme and upcoming films

In WordPress, edit the homepage, select the **Kinokava ja tulevased filmid** page template, and save. Keep `[kinola_events]` in the page content; its filters and ticket links work as before.

The **Tulekul** sidebar lists published films imported by Kinola that have **no events attached yet**. A film is excluded if it has any published WordPress event, past or future, or if Kinola's imported `events` field contains an event. This includes events beyond the shortcode's event limit and events hidden by a date or venue filter. Films are queried directly from Kinola's film post type, independently of the event list. Films are sorted by release date, earliest first, with title as a tiebreaker. Films without a release date appear last. Each film links to its Kinola film detail page. Duplicate Kinola film IDs appear only once.

Films with a Kinola `release_date` before today are excluded, using the site's timezone. Films releasing today or later remain eligible; films without a release date are not excluded by this check. Films with past screenings do not appear. Films must have been imported into WordPress to appear in this list; Kinola's film importer can import films before an event is created.

The list is rebuilt from imported WordPress data on each request. The existing Kinola import schedule keeps that data updated. No additional API request or plugin modification is required.

The template shows the programme and sidebar side by side from 960px. Smaller screens show the sidebar below the programme. If there are no eligible films or Kinola is inactive, the entire sidebar is omitted and the programme uses the full width.

After editing styles in `resources/css/kinola.css`, run `npm run production` from the theme directory to update the served CSS.
