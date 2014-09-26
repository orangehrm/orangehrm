/********************CheckUserEmailAddress*********************
*                                                             *
*                Simple Email Parsing Code.                   *
*                                                             *
* Syntax:                                                     *
* ${CheckUserEmailAddress} "$user_input_var" "$result_var"    *
*                                                             *
* "$user_input_var":  User's input in custom dialog           *
* "$result_var"    :  $result_var=1  (invalid e-mail address) *
*                                                             *
* Example:                                                    *
*	${CheckUserEmailAddress} "$0" "$R0"						  *
*	  $R0="1"  user entered an invalid e-mail address		  *
*                                                             *
***************************************************************/

!ifndef _CheckUserEmailAddress_NSH_
!define _CheckUserEmailAddress_NSH_

!include "WordFunc.nsh"
!insertmacro WordFind
!insertmacro StrFilter

Function CheckUserEmailAddress

    !define CheckUserEmailAddress "!insertmacro CheckUserEmailAddressCall"

    !macro CheckUserEmailAddressCall _INPUT _RESULT
	Push "${_INPUT}"
	Call CheckUserEmailAddress
	Pop ${_RESULT}
    !macroend

    Exch $R0
    Push $R1
    Push $R2
    Push $R3
    Push $R4
    Push $R5

    #count the number of @'s more than one is invalid, less than one is invalid
    ${WordFind} "$R0" "@" "*" $R1
    StrCmp "1" "$R1" lbl_check2 lbl_error

 lbl_check2:
    #count the number of words delimited by @ it should be 2.
    ${WordFind} "$R0" "@" "#" $R1
    StrCmp "2" "$R1" lbl_check3 lbl_error

 lbl_check3:
    #Split the words into user and domain
    ${WordFind} "$R0" "@" "+1" $R2
    ${WordFind} "$R0" "@" "-1" $R3
    #Determine if either of the fields contain special RFC822 characters
    ${StrFilter} "$R2" "" "" '()<>,;:\"[]' $R1
    StrCmp "$R2" "$R1" 0 lbl_error
    ${StrFilter} "$R3" "" "" '()<>,;:\"[]' $R1
    StrCmp "$R3" "$R1" lbl_check4 lbl_error

 lbl_check4:
    #Determine the number of fields in user and domain, check to see
    #the number of delimiter is one less than the number of words.
    StrCpy $R4 0
    StrCpy $R5 0
    ${WordFind} "$R2" "." "*" $R4
    ${WordFind} "$R2" "." "#" $R5

    StrCmp "$R4" "$R2" lbl_check5
    StrCmp "$R5" "$R2" lbl_check5

    IntOp $R4 $R4 + 1
    StrCmp "$R4" "$R5" 0 lbl_error

 lbl_check5:
    StrCpy $R4 0
    StrCpy $R5 0
    ${WordFind} "$R3" "." "*" $R4
    ${WordFind} "$R3" "." "#" $R5

    StrCmp "$R3" "$R4" lbl_error
    StrCmp "$R3" "$R5" lbl_error

    IntOp $R4 $R4 + 1
    StrCmp "$R4" "$R5" 0 lbl_error

 lbl_check6:
    # make sure there is at least one "." in the domain section.
    ${WordFind} "$R3" "." "*" $R1
    IntCmp 1 $R1 lbl_end lbl_end lbl_error
 	StrCpy $R0 0

 lbl_error:
    StrCpy $R0 1

 lbl_end:
    Pop $R5
    Pop $R4
    Pop $R3
    Pop $R2
    Pop $R1
    Exch $R0
FunctionEnd

!endif