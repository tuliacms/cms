# Structure

## Events

- `structure.changed` - When any of the elements in structure was changed (added, updated, moved, removed). Arguments:
  - `structure` - The whole new structure.
- `structure.section.removed` - When section were removed. Arguments:
  - `section` - Section object (with `id` property).
- `structure.row.removed` - When row were removed. Arguments:
  - `row` - Row object (with `id` property).
- `structure.column.removed` - When column were removed. Arguments:
  - `column` - Column object (with `id` property).
- `structure.block.removed` - When block were removed. Arguments:
  - `block` - Block object (with `id` property).
