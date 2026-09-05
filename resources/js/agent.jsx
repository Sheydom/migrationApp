import { createRoot } from "react-dom/client";
import MigrationAgent from './components/MigrationAgent';

console.log('agent.jsx loading');

function mountWidget() {
    const element = document.getElementById("migration-agent");
    if (element && !element.dataset.mounted) {
        console.log("mounting React");
        element.dataset.mounted = "true";
        const root = createRoot(element);
        root.render(<MigrationAgent />);
    }
}

// Try immediately
mountWidget();

// Also watch for it appearing later (Livewire swaps, lazy widgets, etc.)
const observer = new MutationObserver(() => mountWidget());
observer.observe(document.body, { childList: true, subtree: true });