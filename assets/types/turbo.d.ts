// @hotwired/turbo ships no types: only the events listened to by the CSRF controller, with the part of their detail it uses
interface TurboFormSubmission {
    readonly formElement: HTMLFormElement
    readonly fetchRequest: { readonly headers: Record<string, string> }
}

interface DocumentEventMap {
    'turbo:submit-start': CustomEvent<{ formSubmission: TurboFormSubmission }>
    'turbo:submit-end': CustomEvent<{ formSubmission: TurboFormSubmission }>
}
