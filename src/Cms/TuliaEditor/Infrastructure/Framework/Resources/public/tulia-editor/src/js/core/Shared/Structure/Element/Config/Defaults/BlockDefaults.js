import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class BlockDefaults {
    stateProperties = {};

    getBlockState(id, primalState, currents) {
        let state = {};

        this.stateProperties[id] = [];

        for (let i in primalState) {
            this.stateProperties[id].push(i);
            state[i] = primalState[i];
        }

        for (let i in currents) {
            state[i] = currents[i];
        }

        return state;
    }

    exportBlockState(id, state) {
        let exportedState = {};

        for (let i in this.stateProperties) {
            exportedState[this.stateProperties[i]] = state[this.stateProperties[i]];
        }

        return ObjectCloner.deepClone(exportedState);
    }
}
