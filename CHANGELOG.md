# Release Notes for Reporter

## 1.2.0 - 2022-03-01

> {warning} This update modifies the permission behavior of the plugin. If you have given permissions to users or user groups for Reporter you will need to re-enable these under the "General" section of Craft's permission utility for each user/group.

### Added
- Process reports via the CLI using `php craft reporter/report --handle=myReport`

### Updated
- Use `$hasCpSection` to insert Reporter navigation link in the control panel. This ensures it's ordered properly in the sidebar by Craft.
- Use Craft's baked-in permissions when using `$hasCpSection` instead of custom one.

## 1.1.0 - 2022-02-17

### Updated
- Swapped `Db::batch($query)` for `$query->batch()` to obtain Craft 3.5+ compatibility

## 1.0.0 - 2022-02-15

### Added
- Initial release!
