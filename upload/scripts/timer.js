function updateTimes(prefix,anz) {
	v = new Date();
	n = new Date();
	o = new Date();
	for (cn = 1; cn <= anz; cn++) {
        if(anz==1)
            bxx = document.getElementById(prefix);
        else
            bxx = document.getElementById(prefix + cn);
		ss  = bxx.title;
		s   = ss - Math.round((n.getTime() - v.getTime()) / 1000.);
		m   = 0;
		h   = 0;
        d   = 0;
		if (s < 0) {
			bxx.innerHTML = "-";
		} else 
        {
			if (s > 59) {
				m = Math.floor(s/60);
				s = s - m * 60;
			}
			if (m > 59) {
				h = Math.floor(m / 60);
				m = m - h * 60;
			}
            if (h > 23) {
				d = Math.floor(h / 24);
				h = h - d * 60;
			}
			if (s < 10) {
				s = "0" + s;
			}
			if (m < 10) {
				m = "0" + m;
			}
            if(d > 0)
                bxx.innerHTML = d+ "g " + h + ":" + m + ":" + s + "";
            else
                bxx.innerHTML = h + ":" + m + ":" + s + "";
		}
		bxx.title = bxx.title - 1;
	}
	window.setTimeout("updateTimes("+anz+");", 999);
}