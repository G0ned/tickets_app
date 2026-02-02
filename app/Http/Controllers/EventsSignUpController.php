<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Events\AttendeSignUpEvent;
use App\Events\CancelSignUpEvent;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use App\Rules\ValidateId; //Función que valida si el DNI/NIE es válido.
use App\Models\Event;
use App\Models\User;

class EventsSignUpController extends Controller
{
    // Con la nueva versión el request y la inscripción al evento se realiza todo directamente desde este controlador.
    // Por ahora el evento ya está definido como el primero activo en la base de datos por lo que no haría falta pasar ningún parámetro ni hacer ninguna consulta a la BD respecto a él.
    //  Modificaciones respecto a la versión anterior: 
        // 1. Tomar todos los datos del formulario de registro de usuario desde aquí.
        // 2. ¿Guardar al nuevo usuario en al base de datos?.
        // 3. Realizar la inscripción al evento.
        // 4. Disparar el evento de inscripción para: 1- Generar el QR (entrada) y 2- Enviar el correo con la entrada adjunta.

    public function store()
    {
        $event = Event::where('is_active', true)->first();//Sujeto a cambios.

        //Recuperación de los datos del formulario.

        $userData = request()->validate([
            'identification' => ['required', 'max:9', new ValidateId(request()->id_type)],
            'firstname' => ['required', 'max:255'],
            'surname' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        $userData['password'] = 'EEUCONOAA1';
        $userData['role'] = 'attendee';

        $info = request()->validate([
            'phone' => ['required', 'max:9'],
            'zip_code' => ['required', 'max:5'],
            'privacy_policy' => ['required', 'boolean'],
            'img_rights_ads' => ['required', 'boolean'],
            'img_rights_web' => ['required', 'boolean'],
            'img_rights_rss' => ['required', 'boolean'],
        ]);

        //Creación del usuario y asistente en la BD.
        return DB::transaction (function () use ($userData, $info, $event){
            $user = User::create($userData);
            if (!$user){
                throw new \Exception('No se ha podido registrar al asistente...');
            }

            $createdUser = User::find($userData['identification']);
            $createdUser->attendee()->create($info);
        
            if($event->is_active){ //Esta comprobación es redundante ya que el evento que se obtiene al inicio está activo. Se mantendrá por ahora

                if($event->capacity <=0)
                    {
                        return back()->withError('El evento no admite más inscripciones. Lamentamos las molestias.')->withInput();
                    }
                elseif(!$event->assistants->contains('user_id', $userData['identification']))
                    {
                        try{
                                $event = Event::where('id', $event->id)->lockForUpdate()->first();
                                if($event->capacity <=0)
                                    {
                                        throw new \Exception('El evento no admite más inscripciones. Lamentamos las molestias.');
                                    }
                                    
                                $attendee = $createdUser->attendee;
                                $event->assistants()->attach($attendee);

                                AttendeSignUpEvent::dispatch($event, $attendee);

                                return redirect('/')->with('success', 'Inscripción realizada correctamente. En breve recibirás un correo con tu entrada al evento.');  
                        }
                        catch (\Exception $e){
                            return back()->withError('No ha sido posible inscribirse al evento.' . $e->getMessage())->withInput();
                        }
                        
                    }
                else
                    {
                        return back()->withError('Ya estás inscrito en este evento.')->withInput();
                    }
            }
        });
    }

    public function destroy($eventId)
    {
        $event = Event::find($eventId);
        $attendee = Attendee::find(auth()->id());

        try{
            return DB::transaction (function () use ($event, $attendee) {
                Event::where('id', $event->id)->lockForUpdate()->first();
                $event->assistants()->detach($attendee);
                CancelSignUpEvent::dispatch($event);
                return redirect('/attendee/dashboard')->with('success', 'Inscripción cancelada correctamente.');
            });    
        }
        catch (\Exception $e){
            return back()->withError('No ha sido posible cancelar la inscripción al evento.' . $e->getMessage())->withInput();
        }
    }
}
