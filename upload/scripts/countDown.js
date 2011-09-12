function countDown( dd ) {
    // init target time
    var target            = new Date( dd );
    this.targetTime        = target.getTime();
   
    /**
     * refresh countdown
     */
    this.refresh = function() {
        var today                 = new Date();
        var currentTime           = today.getTime();
        // time left
        this._leftMilliseconds    = (this.targetTime - currentTime);
        this._leftSeconds         = Math.floor( this._leftMilliseconds / 1000 );
        this._leftMinutes         = Math.floor( this._leftSeconds / 60 );
        this._leftHours           = Math.floor( this._leftMinutes / 60 );
        // no module
        this.leftDays             = Math.floor( this._leftHours / 24 );
        // for print
        this.leftMilliseconds     = this._leftMilliseconds % 1000;
        this.leftSeconds          = this._leftSeconds % 60;
        this.leftMinutes          = this._leftMinutes % 60;
        this.leftHours            = this._leftHours % 24;
    }
    this.refresh();
}
