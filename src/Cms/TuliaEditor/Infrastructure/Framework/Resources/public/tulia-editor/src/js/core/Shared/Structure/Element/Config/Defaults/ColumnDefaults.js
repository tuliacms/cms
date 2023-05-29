import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const getColumnState = function (currents) {
    return {
        sizes: currents?.sizes ?? {
            xxl: null,
            xl: null,
            lg: null,
            md: null,
            sm: null,
            xs: null,
        },
    };
};

export const exportColumnState = function (state) {
    return {
        sizes: ObjectCloner.deepClone(state.sizes),
    };
};
