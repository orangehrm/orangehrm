# Usage:
#   Push "Tcp" or "Udp"
#   Push "port_number"
#   Call IsPortOpen
#   Pop $0 ; "open" or "closed" or anything else for error
#
# Or with the LogicLib
#   ${If} ${TCPPortOpen} 80
#   ${EndIf}
#   ${If} ${UDPPortOpen} 137
#   ${EndIf}
#
Function IsPortOpen

  Exch $R0 # port to check
  Exch
  Exch $R1
  Push $0
  Push $1
  Push $2

  System::Call 'iphlpapi::Get$R1Table(*i.r0, *i .r1, i 1) i .r2'
  ${If} $2 != 122 # ERROR_INSUFFICIENT_BUFFER
    StrCpy $R0 ""
    Pop $2
    Pop $1
    Pop $0
    Exch $R1
    Exch
    Exch $R0
    Return
  ${EndIf}

  System::Alloc $1
  Pop $0

  System::Call 'iphlpapi::Get$R1Table(ir0, *i r1, i 1) i .r2'
  ${If} $2 != 0 # NO_ERROR
    System::Free $0
    StrCpy $R0 ""
    Pop $2
    Pop $1
    Pop $0
    Exch $R1
    Exch
    Exch $R0
    Return
  ${EndIf}

  Push $3
  Push $4
  Push $5

  System::Call *$0(i.r2)
  IntOp $2 $2 - 1
  ${For} $3 0 $2
    IntOp $4 $0 + 4  # skip dwNumEntries
    ${If} $R1 == "Tcp"
      IntOp $5 $3 * 20 # sizeof(MIB_TCPROW)
      IntOp $4 $4 + $5 # skip to entry
      System::Call *$4(i,i,i.r4,i,i)
    ${Else}
      IntOp $5 $3 * 8 # sizeof(MIB_UDPROW)
      IntOp $4 $4 + $5 # skip to entry
      System::Call *$4(i,i.r4)
    ${EndIf}
    System::Call ws2_32::ntohs(ir4)i.r4

    ${If} $4 = $R0
      StrCpy $R0 "open"
      ${Break}
    ${EndIf}
  ${Next}

  ${If} $R0 != "open"
    StrCpy $R0 "closed"
  ${EndIf}

  System::Free $0

  Pop $5
  Pop $4
  Pop $3
  Pop $2
  Pop $1
  Pop $0
  Exch $R1
  Exch
  Exch $R0

FunctionEnd

# LogicLib macros for IsPortOpen

!include LogicLib.nsh

!macro _PortOpen _a _b _t _f
  !insertmacro _LOGICLIB_TEMP
  Push `${_a}`
  Push `${_b}`
  Call IsPortOpen
  Pop $_LOGICLIB_TEMP
  !insertmacro _== $_LOGICLIB_TEMP "open" `${_t}` `${_f}`
!macroend
!define PortOpen `PortOpen`

!macro _TCPPortOpen _a _b _t _f
  !insertmacro _PortOpen Tcp `${_b}` `${_t}` `${_f}`
!macroend
!define TCPPortOpen `"" TCPPortOpen`

!macro _UDPPortOpen _a _b _t _f
  !insertmacro _PortOpen Udp `${_b}` `${_t}` `${_f}`
!macroend
!define UDPPortOpen `"" UDPPortOpen`