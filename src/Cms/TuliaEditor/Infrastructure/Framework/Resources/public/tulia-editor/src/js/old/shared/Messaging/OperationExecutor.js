export default class OperationExecutor {
    event;
    listeners;
    success;
    fail;

    ended = false;
    listenersLeftToCall = 0;
    startedAt;
    // Time in miliseconds.
    waitingTime = 150;
    waitingInterval;

    constructor (event, listeners, success, fail) {
        this.event = event;
        this.listeners = listeners;
        this.success = success;
        this.fail = fail;

        this.listenersLeftToCall = this.listeners.length;
        this.startedAt = (new Date()).getTime();

        this.decideToSuccessOrFail();

        for (let i in listeners) {
            let operation = listeners[i];
            operation.listener.call(this, event.body, (data) => {
                this.listenersLeftToCall--;
                this.storeData(data);
                this.decideToSuccessOrFail();
            }, () => {
                this.decideToSuccessOrFail();
            });
        }

        this.waitingInterval = setInterval(() => {
            this.decideToSuccessOrFail();
        }, 10);
    }

    end () {
        clearInterval(this.waitingInterval);
        this.ended = true;
    }

    storeData (data) {
        if (typeof(data) === 'object') {
            this.data = {...data, ...this.data};
        }
    }

    decideToSuccessOrFail () {
        if (this.ended) {
            return;
        }

        if (this.listenersLeftToCall === 0) {
            this.success(this.event, this.data);
            this.end();
        }

        if (this.waitingTimeExceeded()) {
            console.error(`TuliaEditor: End operation with fail. Did You forget to call "success" or "fail" callback in some listener of "${this.event.header.name}" operation?`);
            this.fail(this.event);
            this.end();
        }
    }

    waitingTimeExceeded () {
        return (new Date()).getTime() - this.startedAt >= this.waitingTime;
    }
}
