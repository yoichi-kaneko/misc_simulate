import { beforeCalculate } from "../functions/calculate";
import { doNashCalculate } from "../functions/nash";
import * as React from 'react';
import { InlineMath } from "react-katex";
import { createRoot } from 'react-dom/client';

// React component without JSX
const ResetButton = React.createElement('button', {
    id: 'reset',
    className: 'btn btn-secondary mg-b-10',
    onClick: function() {
        // Same logic as the original reset function
        document.querySelectorAll('#nash_block input.form-control').forEach((input) => {
            const element = input as HTMLInputElement;
            if (!element.readOnly) {
                const defaultVal = element.getAttribute('default_val') || '';
                element.value = defaultVal;
            }
        });
    }
}, 'Reset');

// KatexExpression component
const KatexExpression = () => {
    // Component will run after mount
    React.useEffect(() => {
        // Get all .katex_exp elements
        document.querySelectorAll('.form-layout .katex_exp').forEach((element) => {
            const expression = element.getAttribute('expression') || '';
            // Render React component
            const root = createRoot(element as HTMLElement);
            root.render(React.createElement(InlineMath, { math: expression }));
        });
    }, []); // Empty dependency array means run only once after initial render

    // This component doesn't render anything itself
    return null;
};

$(function(){
    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('nash_spinner');
            doNashCalculate();
        }
    });

    // Reset button jQuery code is removed

    // KaTeX rendering code is removed and replaced with React component

    // Render React components
    const resetButtonContainer = document.getElementById('reset-button-container');
    if (resetButtonContainer) {
        const root = createRoot(resetButtonContainer);
        root.render(ResetButton);
    }

    // Render KatexExpression component
    const nashBlock = document.getElementById('nash_block');
    if (nashBlock) {
        // Create a container div for the KatexExpression component
        const container = document.createElement('div');
        container.style.display = 'none'; // Hide the container as it doesn't render anything visible
        nashBlock.appendChild(container);

        // Create root and render the component
        const root = createRoot(container);
        root.render(React.createElement(KatexExpression));
    }
});
