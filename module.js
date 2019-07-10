M.local_badgecerts = {};

M.local_badgecerts.init = function(Y) {

    Y.on('submit', function(e) {
        Y.one('#formactionselect').get('options').each(function() {
            if (this.get('selected') && this.get('value') == '') {
                // No action selected.
                e.preventDefault();
            }
        });
        var ok = false;
        Y.all('input.usercheckbox').each(function() {
            if (this.get('checked')) {
                ok = true;
            }
        });
        if (!ok) {
            // No checkbox selected.
            e.preventDefault();
        }
    }, '#studentsform');

    Y.on('click', function() {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', 'checked');
        });
    }, '#checkall');

    Y.on('click', function() {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', '');
        });
    }, '#checknone');

    Y.on('click', function() {
        Y.all('input.usercheckbox').each(function() {
            if (this.get('value') == 0) {
                this.set('checked', 'checked');
            }
        });
    }, '#checknos');
};
