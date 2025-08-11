// A simple object to act as an event emitter.
const eventBus = {
  events: {},
  on(event, callback) {
    if (!this.events[event]) {
      this.events[event] = [];
    }
    this.events[event].push(callback);
  },
  emit(event, data = {}) {
    if (this.events[event]) {
      this.events[event].forEach(callback => callback(data));
    }
  },
};

export default eventBus;