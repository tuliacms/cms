export default class OperationPromise {
    nativePromise;
    operation;
    body;
    numberOfRequiredDeliveryConfirmations;

    resolveCallback;
    rejectCallback;

    confirmations = 0;
    rejections = 0;

    responseBody = {};

    constructor (operation, body, numberOfRequiredDeliveryConfirmations) {
        this.numberOfRequiredDeliveryConfirmations = numberOfRequiredDeliveryConfirmations;
        this.operation = operation;
        this.body = body;

        this.nativePromise = new Promise((resolve, reject) => {
            this.resolveCallback = resolve;
            this.rejectCallback = reject;
        });
    }

    resolve (body) {
        this.responseBody = {...body, ...this.responseBody};
        this.confirmations++;
        this.decideSuccessOrFail();
    }

    reject () {
        this.rejections++;
        this.decideSuccessOrFail();
    }

    decideSuccessOrFail() {
        if (this.confirmations + this.rejections !== this.numberOfRequiredDeliveryConfirmations) {
            return;
        }

        if (this.confirmations === this.numberOfRequiredDeliveryConfirmations) {
            this.resolveCallback(this.responseBody);
        } else {
            this.rejectCallback();
        }
    }

    getNativePromise () {
        return this.nativePromise;
    }
}
