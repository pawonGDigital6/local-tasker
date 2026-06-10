/**
 * Creates a reactive signal with a getter and setter.
 * Subscribers are notified when the signal value changes.
 *
 * @param {any} initialValue - The initial value of the signal.
 * @returns {[() => any, (newValue: any) => void]} - A tuple with getter and setter functions.
 */
const createSignal = ( initialValue ) => {
	let value = initialValue;
	const subscribers = new Set();

	const get = () => {
		if ( currentEffect ) {
			subscribers.add( currentEffect );
		}
		return value;
	};

	const set = ( newValue ) => {
		if ( value !== newValue ) {
			value = newValue;
			subscribers.forEach( ( subscriber ) => subscriber() );
		}
	};

	return [ get, set ];
};

// A stack to manage nested effects
let currentEffect = null;
const effectStack = [];

/**
 * Creates a reactive effect that tracks dependencies and re-executes when signals change.
 *
 * @param {Function} callback - The effect function to execute.
 */
const createEffect = ( callback ) => {
	const effect = () => {
		cleanup( effect ); // Clear any previous dependencies
		effectStack.push( effect );
		currentEffect = effect;
		callback();
		effectStack.pop();
		currentEffect = effectStack[ effectStack.length - 1 ] || null;
	};

	effect.dependencies = new Set();
	effect();
};

/**
 * Cleans up dependencies for an effect.
 *
 * @param {Function} effect - The effect function to clean up.
 */
const cleanup = ( effect ) => {
	effect.dependencies.forEach( ( signal ) => signal.delete( effect ) );
	effect.dependencies.clear();
};

export { createSignal, createEffect };
