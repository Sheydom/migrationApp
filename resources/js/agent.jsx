import { createRoot } from "react-dom/client";
import MigrationAgent from "./components/MigrationAgent";
import "../css/app.css";

console.log("agent.jsx loading");

function mountWidget() {
    const element = document.getElementById("migration-agent");
    if (element && !element.dataset.mounted) {
        console.log("mounting react");
        element.dataset.mounted = "true";
        const root = createRoot(element);
        root.render(<MigrationAgent />);
    }
}


mountWidget();

// DOM observer whenever livewire/laravel/filament manipulates dom mount react widhtet again
const observer = new MutationObserver(() => mountWidget());
observer.observe(document.body, { childList: true, subtree: true });
