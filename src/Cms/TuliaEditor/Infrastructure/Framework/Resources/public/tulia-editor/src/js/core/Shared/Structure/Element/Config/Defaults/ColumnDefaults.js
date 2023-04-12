import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const getColumnState = function (currents) {
    return {
        sizes: currents?.sizes ?? {
            xxl: { size: null },
            xl: { size: null },
            lg: { size: null },
            md: { size: null },
            sm: { size: null },
            xs: { size: null },
        },
    };
};

export const exportColumnState = function (state) {
    return {
        sizes: ObjectCloner.deepClone(state.sizes),
    };
};
