var expect = require('chai').expect,
	assert = require("assert"),
	Browser = require('zombie');

var browser = new Browser({
	site: "http://localhost/"
});

describe("Given I visit the home page", function (done) {

	before(function (done) {
		browser.visit('/', done);
	});

	it("I can see a random question", function () {
		expect(browser.text('body')).to.contain("Random question");
	});
});