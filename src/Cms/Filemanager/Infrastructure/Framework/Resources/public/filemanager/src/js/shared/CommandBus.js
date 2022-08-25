export default class {
    commands = [];

    command (cmd, callable) {
        this.commands.push({
            command: cmd,
            callable: callable,
        });
    }

    execute (cmd, ...args) {
        for (let i = 0; i < this.commands.length; i++) {
            if (this.commands[i].command === cmd) {
                this.commands[i].callable.apply(null, args);
            }
        }
    }
}
