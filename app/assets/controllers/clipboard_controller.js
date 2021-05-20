import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="clipboard" attribute will cause
 * this controller to be executed. The name "clipboard" comes from the filename:
 * clipboard_controller.js -> "clipboard"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        this.element.addEventListener('click', () => {
            /* Get the text field */
            var copyText = this.element.previousElementSibling;

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */

            /* Copy the text inside the text field */
            document.execCommand("copy");

            /* Alert the copied text */
            alert("Copied the text: " + copyText.value);
        });
    }
}
