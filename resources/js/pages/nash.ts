import { beforeCalculate } from "../functions/calculate";
import { doNashCalculate } from "../functions/nash";
import katex from "katex";
import * as React from 'react';
import * as ReactDOM from 'react-dom';
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

$(function(){
    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('nash_spinner');
            doNashCalculate();
        }
    });

    // Reset button jQuery code is removed

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression') || '', element, {
            throwOnError: false
        });
    });

    // Render React component using modern API
    const resetButtonContainer = document.getElementById('reset-button-container');
    if (resetButtonContainer) {
        const root = createRoot(resetButtonContainer);
        root.render(ResetButton);
    }
});
