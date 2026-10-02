import { Controller } from '@hotwired/stimulus'
// Only the OTP plugin, not Tabler's whole JS bundle
import OtpInput from '@tabler/core/js/src/otp-input'

// Turns the <input> inside a `.otp` wrapper into Tabler's one-slot-per-character code field.
// Tabler initialises it once on page load; a Stimulus controller also covers the pages rendered by Turbo.
export default class extends Controller {
    connect() {
        this.otp = OtpInput.getOrCreateInstance(this.element)
    }

    disconnect() {
        this.otp.dispose()
    }
}
