#!/bin/bash

if [[ $1 == 'stop' ]]; then
		echo -e '';
        screen -S lbot[1] -X quit
		echo -e "[>>] LBot ( 0.0 ) zostal wylaczony [<<]"
elif [[ $1 == 'start' ]]; then
		echo -e '';
        screen -dmS lbot[1] php core.php -i 1

		echo -e "[>>] LBot ( 0.0 ) zostal uruchomiony [<<]"
elif [[ $1 == 'restart' ]]; then
		echo -e '';
        screen -S lbot[1] -X quit
        screen -dmS lbot[1] php core.php -i 1
		echo -e "[>>] LBot ( 0.0 ) zostal zrestartowany [<<]"
else
	echo -e '';
	echo -e "[>>] Uzycie: ./run.sh {start/stop/restart} [<<]"
	echo -e "[>>] start - uruchomienie LBot [<<]"
	echo -e "[>>] stop - wylaczenie LBot [<<]"
	echo -e "[>>] restart - restart LBot [<<]"
	echo -e '';
 fi
