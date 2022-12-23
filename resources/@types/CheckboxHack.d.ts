interface CheckboxHack {
  updateAriaExpanded(checkbox: HTMLInputElement): void;
  bindUpdateAriaExpandedOnInput(
    checkbox: HTMLInputElement
  ): CheckboxHackListeners;
  bindToggleOnClick(
    checkbox: HTMLInputElement,
    button: HTMLElement
  ): CheckboxHackListeners;
  bindToggleOnEnter(checkbox: HTMLInputElement): CheckboxHackListeners;
  bindDismissOnClickOutside(
    window: Window,
    checkbox: HTMLInputElement,
    button: HTMLElement,
    target: Node
  ): CheckboxHackListeners;
  bindDismissOnFocusLoss(
    window: Window,
    checkbox: HTMLInputElement,
    button: HTMLElement,
    target: Node
  ): CheckboxHackListeners;
  bind(
    window: Window,
    checkbox: HTMLInputElement,
    button: HTMLElement,
    target: Node
  ): CheckboxHackListeners;
  unbind(
    window: Window,
    checkbox: HTMLInputElement,
    button: HTMLElement,
    listeners: CheckboxHackListeners
  ): void;
}

interface CheckboxHackListeners {
  onUpdateAriaExpandedOnInput?: EventListenerOrEventListenerObject;
  onToggleOnClick?: EventListenerOrEventListenerObject;
  onDismissOnClickOutside?: EventListenerOrEventListenerObject;
  onDismissOnFocusLoss?: EventListenerOrEventListenerObject;
}
