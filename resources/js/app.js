import './bootstrap';

// NOTE: Livewire 3 bundles and starts Alpine itself (exposes it as window.Alpine).
// Importing and calling Alpine.start() here boots a second instance, which breaks
// magics like $wire inside closures (e.g. confirm modal action callbacks) and
// emits a "Detected multiple instances of Alpine running" warning in the console.
// If you need to register plugins/directives, do it via `document.addEventListener
// ('alpine:init', () => window.Alpine.plugin(...))` instead of importing Alpine here.
