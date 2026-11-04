// Run with: node tests/time-interval-test.js
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const source = fs.readFileSync(require('path').join(__dirname, '../js/book-frontend.js'), 'utf8');
const context = {};
vm.createContext(context);
vm.runInContext(source.slice(source.indexOf('function dsPreparationStep(')), context);
function slots(ranges, step, windows) {
  const increment = context.dsPreparationStep(ranges, step, windows);
  const result = [];
  for (let minute = 0, index = 0; minute < 1440; index++, minute += increment(index)) {
    assert(index < 1441, 'time grid must terminate');
    result.push(minute);
  }
  return result;
}
const windows = [{from:'12:00', to:'14:00', minutes:20}, {from:'18:00', to:'19:00', minutes:10}];
let grid = slots([], 15, windows);
assert(grid.includes(720) && grid.includes(740) && grid.includes(760));
assert(!grid.includes(735) && !grid.includes(750), 'default slots must be replaced within window');
assert(grid.includes(705) && grid.includes(840) && grid.includes(855), 'default interval outside windows');
assert(grid.includes(1090) && !grid.includes(1095), 'independent second window');
grid = slots([['00:00', '12:23']], 15, windows);
assert(grid.includes(743), 'exact preparation boundary is preserved');
grid = slots([], 15, [{from:'23:50', to:'01:00', minutes:20}]);
assert(grid.includes(10) && grid.includes(30) && grid.includes(50) && !grid.includes(15), 'overnight interval anchored to previous day');
grid = slots([], 15, [...windows, {from:'12:00',to:'13:00',minutes:5}]);
assert(!grid.includes(725), 'first overlap wins');
grid = slots([], 15, []);
assert.strictEqual(grid.length, 96, 'no windows preserves fixed grid');
assert(slots([], 15, [{from:'12:00',to:'14:00',minutes:0}]).length <= 1440, 'invalid zero cannot loop');
console.log('Time-interval frontend regression checks passed.');
