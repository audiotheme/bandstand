/* jshint browserify: true */

module.exports = {
	isAddressEmpty: function() {
		return ! ( this.address || this.city || this.region || this.postal_code || this.country );
	},

	formatCityRegionPostalCode: function() {
		var location = '';

		if ( this.city ) {
			location += this.city;
		}

		if ( this.region ) {
			location = ( '' === location ) ? this.region : location + ', ' + this.region;
		}

		if ( this.postal_code ) {
			location = ( '' === location ) ? this.postal_code : location + ' ' + this.postal_code;
		}

		return location;
	}
};
