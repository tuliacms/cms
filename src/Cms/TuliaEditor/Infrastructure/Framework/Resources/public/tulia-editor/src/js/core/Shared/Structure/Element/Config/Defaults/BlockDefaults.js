import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class BlockDefaults {
    stateProperties = {};

    getBlockState(id, primalState, currents) {
        let state = {};

        this.prepareDefaultState(primalState);
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

        for (let i in this.stateProperties[id]) {
            exportedState[this.stateProperties[id][i]] = state[this.stateProperties[id][i]];
        }

        return ObjectCloner.deepClone(exportedState);
    }

    /**
     * Default config options has prefix for two reasons. First is to distinguish them from
     * the component ones. Second - most important - to prevent overwriting by component.
     */
    prepareDefaultState(state) {
        state.__visibility = {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null};
        state.__margin = {
            left: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
            top: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
            right: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
            bottom: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
        };
        state.__padding = {
            left: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
            top: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
            right: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
            bottom: {xxl: null, xl: null, lg: null, md: null, sm: null, xs: null},
        };
    }
}
