[ req ]
default_bits = 2048
default_keyfile = {{$ssl['web_app_name']}}.key
prompt = no
distinguished_name = req_distinguished_name

[ req_distinguished_name ]
countryName            = "{{$ssl['country_name']}}"              
stateOrProvinceName    = "{{$ssl['state_name']}}"
localityName           = "{{$ssl['locality_name']}}"               
organizationName       = "{{$ssl['org_name']}}"           
organizationalUnitName = "{{$ssl['org_unit_name']}}"           
commonName             = "{{$ssl['common_name']}}"           
emailAddress           = "{{$ssl['email_address']}}"    