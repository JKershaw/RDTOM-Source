var expect = require('chai').expect,
	assert = require("assert"),
	Browser = require('zombie');

var browser = new Browser({
	site: "http://localhost/"
});

describe("Given I visit the profile", function (done) {

	before(function (done) {
		browser.visit('/profile', done);
	});

	it("I can see the login prompt", function () {
		expect(browser.text('body')).to.contain("Login");
	});
});