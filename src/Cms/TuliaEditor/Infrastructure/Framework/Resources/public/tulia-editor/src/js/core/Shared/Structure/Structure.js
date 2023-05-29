export default class Structure {
    constructor(
        instantiator,
    ) {
        this.instantiator = instantiator;
    }

    block(identity) {
        return this.instantiator.instance(this.standarizeIdentity(identity, 'block'));
    }

    column(identity) {
        return this.instantiator.instance(this.standarizeIdentity(identity, 'column'));
    }

    row(identity) {
        return this.instantiator.instance(this.standarizeIdentity(identity, 'row'));
    }

    section(identity) {
        return this.instantiator.instance(this.standarizeIdentity(identity, 'section'));
    }

    standarizeIdentity(identity, type) {
        if (identity.id && identity.type) {
            return identity;
        }

        if (identity.id) {
            return {
                id: identity.id,
                type: type,
            };
        }

        if (typeof identity === 'string') {
            return {
                id: identity,
                type: type,
            };
        }

        throw new Error('Identity must be string or object with "id" and "type" properties.');
    }
}
