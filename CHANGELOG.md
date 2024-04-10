# Release Notes for Reporter

## 2.3.0 - 2024-04-10

### Added
- Update league/fractal to ^0.20.1

## 2.2.0 - 2023-09-29

### Added
- Include new user avatars in the Exports view
- Ensure all exports have an `uploaderId`. When ran in the console this defaults to the first admin

## 2.1.0 - 2023-09-29

### Fixed
- Downgrade league/fractal to ^0.18.0 to play nicely with Element API

## 2.0.0 - 2023-06-22

### Added
- Craft 4 support (Thanks, [@darinlarimore](https://github.com/darinlarimore)!)

## 1.4.0 - 2023-05-18

### Added
- Allow ability to change the size of the batch when processing reports via the `batchSize` config setting

## 1.3.1 - 2022-10-03

### Fixed
- Only set an author if the user's identity can be determined

## 1.3.0 - 2022-09-08

### Added
- Allow the ability to "skip" over an item by returning an empty array

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
