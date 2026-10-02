import { Controller } from '@hotwired/stimulus'
// Only the OTP plugin, not Tabler's whole JS bundle
import OtpInput from '@tabler/core/js/src/otp-input'

// Turns the <input> inside a `.otp` wrapper into Tabler's one-slot-per-character code field,
// and submits its form as soon as the code is complete.
// Tabler initialises it once on page load; a Stimulus controller also covers the pages rendered by Turbo.
export default class extends Controller {
    connect() {
        this.otp = OtpInput.getOrCreateInstance(this.element)
        this.element.addEventListener('complete.bs.otpInput', this.submit)
    }

    disconnect() {
        this.element.removeEventListener('complete.bs.otpInput', this.submit)
        this.otp.dispose()
    }

    // requestSubmit() fires the submit event, so the CSRF and Turbo listeners still run
    submit = () => {
        this.element.closest('form')?.requestSubmit()
    }
}
