import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const getSectionState = function (currents) {
    return {
        containerWidth: currents?.containerWidth ?? 'default',
        anchorId: currents?.anchorId ?? null,
    };
};

export const exportSectionState = function (state) {
    return {
        containerWidth: ObjectCloner.deepClone(state.containerWidth),
        anchorId: ObjectCloner.deepClone(state.anchorId),
    };
};
