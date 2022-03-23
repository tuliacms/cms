# Selection

## Events

- `structure.selection.selected` - Element on structure or in canvas was clicked/selected. Arguments:
  - `type` - type of the element (block, column, row, section)
  - `id` - ID of the element
- `structure.selection.deselected` - Clicked outlide the structure. When we deselect selected element.



## Events callstack

- Local `structure.selection.select` (`type`, `id`)
- Local `structure.selection.selected` (`type`, `id`)

# Hovering

## Events

- `structure.hovering.enter` - Mouseenter on element.
- `structure.hovering.leave` - Mouseleave on element.
- `structure.hovering.hover` - Calculated hover on element.
- `structure.hovering.clear` - When none of elements is hovering.
